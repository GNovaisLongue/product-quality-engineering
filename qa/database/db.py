"""Direct MySQL access for schema + persistence assertions."""
import os
import pymysql


def connect():
    return pymysql.connect(
        host=os.getenv("MYSQL_HOST", "localhost"),
        port=int(os.getenv("MYSQL_PORT", "9906")),
        user=os.getenv("MYSQL_USER", "root"),
        password=os.getenv("MYSQL_PASSWORD", "root"),
        database=os.getenv("MYSQL_DATABASE", "ecommerce_products"),
        charset="utf8mb4",
        autocommit=True,
    )


def fetch_product_row(sku: str) -> dict | None:
    with connect() as conn, conn.cursor(pymysql.cursors.DictCursor) as cur:
        cur.execute(
            "SELECT `sku`,`name`,`price`,`product_type`,`product_attribute` "
            "FROM `products` WHERE `sku` = %s",
            (sku,),
        )
        return cur.fetchone()


def column_info() -> dict:
    with connect() as conn, conn.cursor() as cur:
        cur.execute(
            "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA "
            "FROM INFORMATION_SCHEMA.COLUMNS "
            "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'"
        )
        return {row[0]: row for row in cur.fetchall()}


def table_charset() -> str:
    with connect() as conn, conn.cursor() as cur:
        cur.execute(
            "SELECT TABLE_COLLATION FROM INFORMATION_SCHEMA.TABLES "
            "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'"
        )
        row = cur.fetchone()
        return row[0] if row else ""
