"""TC-VAL-001..018 — equivalence partitions + boundary values per subtype."""
import pytest

from qa.data import factory
from qa.schemas.product import assert_error

pytestmark = [pytest.mark.api]


def _expect_422(api, payload, field=None):
    r = api.create(payload)
    assert r.status_code == 422, f"expected 422, got {r.status_code}: {r.text} for {payload}"
    err = assert_error(r.json(), code="VALIDATION_ERROR")
    if field:
        details = err.get("details", {})
        assert field in details, f"expected '{field}' in details {details}"
    return err


# --- DVD partitions ---
@pytest.mark.parametrize("size", [0, -3, "abc", "", None])
def test_dvd_bad_sizes(api, size):
    p = factory.dvd_payload(size=size)
    if size is None:
        p.pop("size")
    _expect_422(api, p, "size")


# --- Book partitions ---
@pytest.mark.parametrize("weight", [0, -1, "abc", "", None])
def test_book_bad_weights(api, weight):
    p = factory.book_payload(weight=weight)
    if weight is None:
        p.pop("weight")
    _expect_422(api, p, "weight")


# --- Furniture partitions ---
@pytest.mark.parametrize("dims", ["200x100", "200xaxb", "200x100x", "abc", "200xx100"])
def test_furniture_malformed_strings(api, dims):
    _expect_422(api, factory.furniture_string_payload(dims=dims), "dimensions")


def test_furniture_zero_segment(api):
    _expect_422(api, factory.furniture_string_payload(dims="0x10x10"))


def test_furniture_missing_dims(api):
    p = {"sku": factory.unique_sku(), "name": "x", "price": 10, "product_type": "Furniture"}
    _expect_422(api, p, "dimensions")


# --- Type discriminator ---
def test_unknown_type(api):
    p = factory.dvd_payload()
    p["product_type"] = "Software"
    _expect_422(api, p, "product_type")


def test_missing_type(api):
    p = factory.dvd_payload()
    p.pop("product_type")
    _expect_422(api, p, "product_type")


# --- Common fields: SKU / price BVA ---
def test_empty_sku(api):
    p = factory.dvd_payload(sku="")
    _expect_422(api, p, "sku")


def test_long_sku_boundary(api):
    ok = factory.dvd_payload(sku="A" * 64)
    r = api.create(ok)
    assert r.status_code == 201, r.text
    api.delete_one(ok["sku"])
    bad = factory.dvd_payload(sku="A" * 65)
    _expect_422(api, bad, "sku")


def test_bad_char_sku(api):
    _expect_422(api, factory.dvd_payload(sku="bad sku!"), "sku")


@pytest.mark.parametrize("price", [0, -1, "free", "", None])
def test_bad_prices(api, price):
    p = factory.dvd_payload(price=price)
    if price is None:
        p.pop("price")
    _expect_422(api, p, "price")


def test_price_boundary_min(api, tracked):
    p = factory.dvd_payload(price=0.01)
    r = api.create(p)
    assert r.status_code == 201, r.text
    tracked.append(p["sku"])


def test_malformed_json(api):
    import requests

    r = requests.post(
        f"{api.base}/api/products",
        data="{not-json",
        headers={"Content-Type": "application/json"},
        timeout=15,
    )
    assert r.status_code == 400, r.text


def test_empty_object(api):
    r = api.create({})
    assert r.status_code in (400, 422), r.text
