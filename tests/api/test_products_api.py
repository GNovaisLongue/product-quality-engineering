"""TC-API-001..010 — CRUD + polymorphism over the REST API."""
import pytest

from qa.data import factory
from qa.schemas.product import assert_envelope, assert_error, assert_product_shape

pytestmark = [pytest.mark.api]


def test_list_returns_seed_products(api):
    r = api.list()
    assert r.status_code == 200
    data = assert_envelope(r.json())
    assert isinstance(data, list) and len(data) >= 3
    by_sku = {p["sku"]: p for p in data}
    for seed in ("SKUTestSKU000", "SKUTestSKU001", "SKUTestSKU002"):
        assert seed in by_sku, f"seed {seed} missing"
    for p in data:
        assert_product_shape(p)


def test_create_dvd(api, tracked):
    payload = factory.dvd_payload()
    r = api.create(payload)
    assert r.status_code == 201, r.text
    prod = assert_envelope(r.json())
    tracked.append(payload["sku"])
    assert prod["product_type"] == "DVD"
    assert prod["product_attribute"] == "500"
    assert prod["attribute"] == {"size": 500}


def test_create_book(api, tracked):
    payload = factory.book_payload()
    r = api.create(payload)
    assert r.status_code == 201, r.text
    prod = assert_envelope(r.json())
    tracked.append(payload["sku"])
    assert prod["product_type"] == "Book"
    assert prod["attribute"]["weight"] == pytest.approx(2.5)


def test_create_furniture_from_fields(api, tracked):
    payload = factory.furniture_payload()
    r = api.create(payload)
    assert r.status_code == 201, r.text
    prod = assert_envelope(r.json())
    tracked.append(payload["sku"])
    assert prod["product_type"] == "Furniture"
    assert prod["product_attribute"] == "200x100x50"


def test_create_furniture_from_legacy_string(api, tracked):
    payload = factory.furniture_string_payload(dims="10x20x30")
    r = api.create(payload)
    assert r.status_code == 201, r.text
    prod = assert_envelope(r.json())
    tracked.append(payload["sku"])
    assert prod["product_attribute"] == "10x20x30"


def test_get_one_roundtrip(api, tracked):
    payload = factory.dvd_payload()
    assert api.create(payload).status_code == 201
    tracked.append(payload["sku"])
    r = api.get_one(payload["sku"])
    assert r.status_code == 200, r.text
    prod = assert_envelope(r.json())
    assert prod["sku"] == payload["sku"]


def test_delete_one(api):
    payload = factory.book_payload()
    assert api.create(payload).status_code == 201
    r = api.delete_one(payload["sku"])
    assert r.status_code == 200
    assert api.get_one(payload["sku"]).status_code == 404


def test_bulk_delete(api):
    skus = []
    for make in (factory.dvd_payload, factory.book_payload):
        p = make()
        assert api.create(p).status_code == 201
        skus.append(p["sku"])
    r = api.delete_many(skus)
    assert r.status_code == 200
    assert assert_envelope(r.json())["deleted"] == 2


def test_get_missing_is_404(api):
    r = api.get_one("NOPE-DOES-NOT-EXIST-1")
    assert r.status_code == 404
    assert_error(r.json(), code="NOT_FOUND")


def test_duplicate_sku_is_409(api, tracked):
    payload = factory.dvd_payload()
    assert api.create(payload).status_code == 201
    tracked.append(payload["sku"])
    r = api.create(payload)
    assert r.status_code == 409, r.text
    assert_error(r.json(), code="CONFLICT")
