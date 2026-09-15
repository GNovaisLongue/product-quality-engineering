"""Contract tests — responses honour openapi/openapi.yaml shapes."""
import pathlib
import pytest
import yaml

from qa.data import factory
from qa.schemas.product import assert_envelope, assert_product_shape

pytestmark = [pytest.mark.contract]

SPEC = pathlib.Path(__file__).resolve().parents[2] / "openapi" / "openapi.yaml"


def test_spec_exists_and_pins_enum():
    assert SPEC.exists(), "openapi/openapi.yaml must exist"
    spec = yaml.safe_load(SPEC.read_text())
    inp = spec["components"]["schemas"]["ProductInput"]["properties"]
    assert inp["product_type"]["enum"] == ["DVD", "Book", "Furniture"]
    prod = spec["components"]["schemas"]["Product"]
    for field in ("sku", "name", "price", "product_type", "product_attribute"):
        assert field in prod["required"], f"{field} must be required"


def test_list_envelope_matches_contract(api):
    body = api.list().json()
    data = assert_envelope(body)
    assert isinstance(body.get("meta", {}).get("count"), int)
    assert body["meta"]["count"] == len(data)
    for p in data:
        assert_product_shape(p)


@pytest.mark.parametrize("make", ["dvd", "book", "fur"])
def test_create_shapes_match_contract(api, tracked, make):
    builders = {
        "dvd": factory.dvd_payload,
        "book": factory.book_payload,
        "fur": factory.furniture_payload,
    }
    payload = builders[make]()
    r = api.create(payload)
    assert r.status_code == 201, r.text
    tracked.append(payload["sku"])
    assert_product_shape(assert_envelope(r.json()))
