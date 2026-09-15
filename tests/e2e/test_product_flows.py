"""TC-E2E-001..003 — Playwright UI flows (polymorphism-aware).

Skipped automatically when Playwright/browsers are unavailable.
Run: `qa/venv/Scripts/python.exe -m pytest tests/e2e` with stack + frontend up.

Note: the app returns from the form via `navigate(-1)`, which strands
direct-URL visits on a blank page. The helper therefore waits for the
create POST and navigates to the list explicitly instead of relying
on the app's back-navigation.
"""
import os
import pytest

pytestmark = [pytest.mark.e2e]

pytest.importorskip("playwright.sync_api", reason="playwright not installed")

from playwright.sync_api import sync_playwright  # noqa: E402

from qa.data import factory  # noqa: E402

UI_BASE = os.getenv("UI_BASE_URL", "http://localhost:3000")


@pytest.fixture(scope="module")
def page():
    with sync_playwright() as pw:
        browser = pw.chromium.launch()
        pg = browser.new_page()
        yield pg
        browser.close()


def _add_product(page, sku, name, price, ptype, attrs: dict):
    page.goto(f"{UI_BASE}/add-product")
    page.fill("#sku", sku)
    page.fill("#name", name)
    page.fill("#price", price)
    page.select_option("#productType", ptype)
    # The attribute inputs re-render on type change; wait for the new
    # fields before filling or the value lands on a detached node.
    first_field = next(iter(attrs))
    page.wait_for_selector(f"#{first_field}", state="visible", timeout=10000)
    for field_id, value in attrs.items():
        page.fill(f"#{field_id}", value)
    with page.expect_response(
        lambda r: r.request.method == "POST", timeout=10000
    ) as resp_info:
        page.click("button[type=submit]")
    resp = resp_info.value
    assert resp.status in (200, 201), f"create POST failed: {resp.status}"
    page.goto(f"{UI_BASE}/list-products")
    page.get_by_text(sku).wait_for(timeout=10000)


@pytest.mark.parametrize(
    "ptype,attrs,expect",
    [
        ("DVD", {"size": "700"}, "Size: 700 MB"),
        ("Book", {"weight": "3.2"}, "Weight: 3.2 KG"),
        (
            "Furniture",
            {"height": "10", "width": "20", "length": "30"},
            "Dimensions: 10x20x30",
        ),
    ],
)
def test_create_product_via_ui(page, tracked, ptype, attrs, expect):
    sku = factory.unique_sku("E2E")
    tracked.append(sku)
    _add_product(page, sku, f"E2E {ptype}", "25", ptype, attrs)
    body = page.content()
    assert expect in body
