"""TC-SEC-001..005 — security regression (BUG-001 SQLi, error leakage)."""
import pytest
import requests

from qa.data import factory

pytestmark = [pytest.mark.api, pytest.mark.security]


def _assert_no_leak(text: str):
    lowered = text.lower()
    # NOTE: user-supplied SKU text is echoed in 404 messages, so a bare
    # "select" probe string may legitimately appear. What must never leak
    # are engine internals (query dumps, driver errors, stacks, legacy HTML).
    for token in ("sqlstate", "sql syntax", "stack trace", "pdoexception",
                  "failed to execute query", "</br>", "mysqladmin"):
        assert token not in lowered, f"possible internals leak ({token!r}) in: {text[:300]}"


def test_sqli_or_bypass_rejected(api):
    p = factory.dvd_payload(sku='" OR "1"="1')
    r = api.create(p)
    assert r.status_code in (400, 409, 422), r.text
    _assert_no_leak(r.text)
    # Table must be intact: list still works, seed present.
    lst = api.list()
    assert lst.status_code == 200
    assert any(x["sku"] == "SKUTestSKU000" for x in lst.json()["data"])


def test_sqli_drop_table_rejected(api):
    p = factory.dvd_payload(sku="'; DROP TABLE products;--")
    r = api.create(p)
    assert r.status_code in (400, 409, 422), r.text
    _assert_no_leak(r.text)
    assert api.list().status_code == 200


def test_sqli_in_attribute_cannot_break_table(api, tracked):
    p = factory.dvd_payload()
    p["size"] = '10") OR ("1"="1'
    r = api.create(p)
    # Either stored literally (201) or rejected (422) — never a 500/SQL dump.
    assert r.status_code in (201, 422), r.text
    _assert_no_leak(r.text)
    if r.status_code == 201:
        tracked.append(p["sku"])
    assert api.list().status_code == 200


def test_error_bodies_have_no_sql(api):
    for probe in ("NOPE-1", "'; SELECT * FROM products; --"):
        r = api.get_one(probe)
        assert r.status_code == 404
        _assert_no_leak(r.text)


def test_bulk_delete_empty_rejected(api):
    r = api.delete_many([])
    assert r.status_code == 422, r.text
    assert api.list().status_code == 200
