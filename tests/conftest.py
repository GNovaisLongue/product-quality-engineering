"""Shared fixtures: API client + per-test SKU cleanup registry."""
import os
import pytest
import requests

from qa.clients.api_client import ProductApi

API_BASE = os.getenv("API_BASE_URL", "http://localhost:8000")


@pytest.fixture(scope="session")
def api_base() -> str:
    return API_BASE


@pytest.fixture(scope="session")
def http() -> requests.Session:
    s = requests.Session()
    yield s
    s.close()


@pytest.fixture(scope="session")
def api(http) -> ProductApi:
    return ProductApi(API_BASE, http)


@pytest.fixture()
def tracked(api: ProductApi):
    """Collect created SKUs; best-effort DELETE at teardown for isolation."""
    skus: list[str] = []
    yield skus
    if skus:
        try:
            api.delete_many(skus)
        except Exception:
            pass


@pytest.fixture(scope="session", autouse=True)
def _require_stack(api: ProductApi):
    r = api.health()
    assert r.status_code == 200, f"backend not healthy at {API_BASE}: {r.status_code} {r.text[:200]}"
