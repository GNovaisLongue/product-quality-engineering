"""Reusable API client — one place for all HTTP shapes (REST + legacy BC)."""
import os
import requests

API_BASE = os.getenv("API_BASE_URL", "http://localhost:8000")


class ProductApi:
    def __init__(self, base_url: str = API_BASE, session: requests.Session | None = None):
        self.base = base_url.rstrip("/")
        self.session = session or requests.Session()

    # -- health / list --
    def health(self) -> requests.Response:
        return self.session.get(f"{self.base}/health", timeout=15)

    def list(self) -> requests.Response:
        return self.session.get(f"{self.base}/api/products", timeout=15)

    def legacy_list(self) -> requests.Response:
        return self.session.get(f"{self.base}/", timeout=15)

    # -- create --
    def create(self, payload: dict) -> requests.Response:
        return self.session.post(f"{self.base}/api/products", json=payload, timeout=15)

    def legacy_create(self, payload: dict) -> requests.Response:
        return self.session.post(f"{self.base}/", json=payload, timeout=15)

    # -- read one --
    def get_one(self, sku: str) -> requests.Response:
        return self.session.get(f"{self.base}/api/products/{sku}", timeout=15)

    # -- delete --
    def delete_many(self, skus: list) -> requests.Response:
        return self.session.request("DELETE", f"{self.base}/api/products", json=skus, timeout=15)

    def delete_one(self, sku: str) -> requests.Response:
        return self.session.delete(f"{self.base}/api/products/{sku}", timeout=15)

    def legacy_delete_many(self, skus: list) -> requests.Response:
        # Legacy 000webhost workaround: POST with SKU array.
        return self.session.post(f"{self.base}/", json=skus, timeout=15)
