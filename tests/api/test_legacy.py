"""TC-API-011..013 — legacy `/` backward compatibility for the React UI."""
import pytest

from qa.data import factory

pytestmark = [pytest.mark.api]


def test_legacy_list_returns_bare_array(api):
    r = api.legacy_list()
    assert r.status_code == 200
    body = r.json()
    assert isinstance(body, list), f"legacy list must stay a bare array, got: {type(body)}"
    assert len(body) >= 3
    first = body[0]
    assert {"sku", "name", "price", "product_type", "product_attribute"} <= set(first)


def test_legacy_create_object(api, tracked):
    payload = factory.dvd_payload()
    r = api.legacy_create(payload)
    assert r.status_code in (200, 201), r.text
    tracked.append(payload["sku"])
    assert api.get_one(payload["sku"]).status_code == 200


def test_legacy_bulk_delete_array(api):
    payload = factory.book_payload()
    assert api.create(payload).status_code == 201
    r = api.legacy_delete_many([payload["sku"]])
    assert r.status_code == 200
    assert api.get_one(payload["sku"]).status_code == 404


def test_legacy_empty_array_rejected(api):
    r = api.legacy_delete_many([])
    assert r.status_code in (400, 422), r.text
