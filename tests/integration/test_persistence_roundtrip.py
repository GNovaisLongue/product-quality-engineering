"""TC-DB-002 — API → Domain → Database → API round-trips per subtype."""
import pytest

from qa.data import factory
from qa.database import db

pytestmark = [pytest.mark.db, pytest.mark.integration]


@pytest.mark.parametrize(
    "make,expected_attr",
    [
        (factory.dvd_payload, "500"),
        (factory.book_payload, "2.5"),
        (factory.furniture_payload, "200x100x50"),
    ],
)
def test_persistence_roundtrip(api, tracked, make, expected_attr):
    payload = make()
    assert api.create(payload).status_code == 201
    tracked.append(payload["sku"])

    row = db.fetch_product_row(payload["sku"])
    assert row is not None, "row must survive persistence"
    assert row["product_type"] == payload["product_type"]
    assert str(row["product_attribute"]) == expected_attr

    back = api.get_one(payload["sku"]).json()["data"]
    assert back["product_attribute"] == expected_attr
