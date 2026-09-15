"""Test-data factory — unique SKUs + per-subtype payload builders."""
import uuid


def unique_sku(prefix: str = "TST") -> str:
    return f"{prefix}-{uuid.uuid4().hex[:8].upper()}"


def dvd_payload(sku: str | None = None, size=500, price=19.99, name="Test DVD") -> dict:
    return {
        "sku": unique_sku("TST-DVD") if sku is None else sku,
        "name": name,
        "price": price,
        "product_type": "DVD",
        "size": size,
    }


def book_payload(sku: str | None = None, weight=2.5, price=15.50, name="Test Book") -> dict:
    return {
        "sku": unique_sku("TST-BOOK") if sku is None else sku,
        "name": name,
        "price": price,
        "product_type": "Book",
        "weight": weight,
    }


def furniture_payload(
    sku: str | None = None,
    height=200,
    width=100,
    length=50,
    price=49.99,
    name="Test Chair",
) -> dict:
    return {
        "sku": unique_sku("TST-FUR") if sku is None else sku,
        "name": name,
        "price": price,
        "product_type": "Furniture",
        "height": height,
        "width": width,
        "length": length,
    }


def furniture_string_payload(sku: str | None = None, dims="200x100x50", price=49.99) -> dict:
    return {
        "sku": unique_sku("TST-FUR") if sku is None else sku,
        "name": "Test Furniture",
        "price": price,
        "product_type": "Furniture",
        "product_attribute": dims,
    }
