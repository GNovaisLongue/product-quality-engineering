"""Lightweight contract helpers mirroring openapi/openapi.yaml shapes."""

REQUIRED_PRODUCT_FIELDS = {"sku", "name", "price", "product_type", "product_attribute"}
ALLOWED_TYPES = {"DVD", "Book", "Furniture"}


def assert_envelope(body: dict, *, expect_data=True) -> dict:
    assert body.get("success") is True, f"expected success envelope, got: {body}"
    assert "data" in body, f"envelope missing 'data': {body}"
    if expect_data:
        assert body["data"] is not None
    return body["data"]


def assert_error(body: dict, *, code: str | None = None) -> dict:
    assert body.get("success") is False, f"expected error envelope, got: {body}"
    err = body.get("error", {})
    assert "code" in err and "message" in err, f"malformed error: {body}"
    if code:
        assert err["code"] == code, f"expected code {code}, got {err['code']}: {body}"
    return err


def assert_product_shape(prod: dict) -> None:
    missing = REQUIRED_PRODUCT_FIELDS - set(prod.keys())
    assert not missing, f"product missing fields {missing}: {prod}"
    assert prod["product_type"] in ALLOWED_TYPES, f"bad type: {prod}"
    assert isinstance(prod["product_attribute"], str) and prod["product_attribute"] != ""
