"""TC-DB-001 — schema matches the hardened domain contract."""
import pytest

from qa.database import db

pytestmark = [pytest.mark.db]


def test_column_types_and_keys():
    info = db.column_info()
    assert info["sku"][1].upper().startswith("VARCHAR(64)"), info["sku"]
    assert info["sku"][3] == "PRI", info["sku"]  # COLUMN_KEY
    assert info["name"][1].upper().startswith("VARCHAR(255)"), info["name"]
    assert info["price"][1].lower().startswith("decimal(10,2)"), info["price"]
    assert info["product_type"][1].upper().startswith("VARCHAR(20)"), info["product_type"]
    assert info["product_attribute"][1].upper().startswith("VARCHAR(255)"), info["product_attribute"]
    for col in ("sku", "name", "price", "product_type", "product_attribute"):
        assert info[col][2] == "NO", f"{col} must be NOT NULL: {info[col]}"


def test_charset_is_utf8mb4():
    collation = db.table_charset()
    assert collation.startswith("utf8mb4"), collation


def test_price_check_rejects_nonpositive():
    conn = db.connect()
    try:
        with conn.cursor() as cur:
            with pytest.raises(Exception):
                cur.execute(
                    "INSERT INTO `products` (`sku`,`name`,`price`,`product_type`,`product_attribute`)"
                    " VALUES ('TCK-PRICE-1','x',0,'DVD','10')"
                )
    finally:
        with conn.cursor() as cur:
            cur.execute("DELETE FROM `products` WHERE `sku`='TCK-PRICE-1'")
        conn.close()


def test_pk_rejects_duplicate():
    row = db.fetch_product_row("SKUTestSKU000")
    assert row is not None
    conn = db.connect()
    try:
        with conn.cursor() as cur:
            with pytest.raises(Exception):
                cur.execute(
                    "INSERT INTO `products` (`sku`,`name`,`price`,`product_type`,`product_attribute`)"
                    " VALUES ('SKUTestSKU000','dup',1,'DVD','10')"
                )
    finally:
        conn.close()
