"""Smoke suite (Phase 6, step 31) — PR gate: health, seed list, one DVD lifecycle."""
import pytest

from qa.data import factory

pytestmark = [pytest.mark.smoke]


def test_health(api):
    r = api.health()
    assert r.status_code == 200
    assert r.json()["data"]["ok"] is True


def test_seed_list(api):
    data = api.list().json()["data"]
    assert len(data) >= 3


def test_dvd_lifecycle(api):
    p = factory.dvd_payload()
    assert api.create(p).status_code == 201
    assert api.get_one(p["sku"]).status_code == 200
    assert api.delete_one(p["sku"]).status_code == 200
    assert api.get_one(p["sku"]).status_code == 404
