"""QA analytics (Phase 7, steps 37–38) — junit.xml → per-area metrics + JSON.

Usage:
    python -m pytest tests/api tests/contract tests/database tests/integration -q --junitxml=reports/junit.xml
    python qa/analytics.py reports/junit.xml
"""
import json
import sys
import xml.etree.ElementTree as ET
from collections import Counter, defaultdict

AREA_BY_PATH = (
    ("smoke", "smoke"),
    ("api", "api"),
    ("contract", "contract"),
    ("database", "database"),
    ("integration", "integration"),
    ("e2e", "e2e"),
    ("unit", "unit"),
)


def area_of(classname: str) -> str:
    lowered = classname.replace("\\", "/").lower()
    for token, area in AREA_BY_PATH:
        if token in lowered:
            return area
    return "other"


def main(path: str) -> None:
    tree = ET.parse(path)
    cases = tree.getroot().iter("testcase")

    total = failures = errors = skipped = 0
    by_area: dict[str, Counter] = defaultdict(Counter)
    by_type: dict[str, Counter] = defaultdict(Counter)
    failures_list: list[str] = []
    suite_time = 0.0

    for tc in cases:
        total += 1
        cls = tc.get("classname", "")
        name = tc.get("name", "")
        suite_time += float(tc.get("time", 0) or 0)
        area = area_of(cls + " " + (tc.get("file", "") or ""))
        status = "passed"
        if tc.find("failure") is not None:
            status = "failed"
            failures += 1
            failures_list.append(f"{cls}::{name}")
        elif tc.find("error") is not None:
            status = "failed"
            errors += 1
            failures_list.append(f"{cls}::{name}")
        elif tc.find("skipped") is not None:
            status = "skipped"
            skipped += 1
        by_area[area][status] += 1
        # product-type dimension: which subtype does the test touch?
        blob = (cls + " " + name).lower()
        for ptype in ("dvd", "book", "furniture"):
            if ptype in blob:
                by_type[ptype][status] += 1

    passed = total - failures - errors - skipped
    metrics = {
        "summary": {
            "total": total,
            "passed": passed,
            "failed": failures + errors,
            "skipped": skipped,
            "pass_rate": round(passed / total * 100, 2) if total else 0,
            "duration_s": round(suite_time, 2),
        },
        "by_area": {k: dict(v) for k, v in sorted(by_area.items())},
        "by_product_type": {k: dict(v) for k, v in sorted(by_type.items())},
        "failures": failures_list,
    }

    with open("reports/qa-metrics.json", "w", encoding="utf-8") as fh:
        json.dump(metrics, fh, indent=2)

    print(f"total={total} passed={passed} failed={failures + errors} "
          f"skipped={skipped} pass_rate={metrics['summary']['pass_rate']}%")
    print("by_area:")
    for area, counts in sorted(by_area.items()):
        print(f"  {area}: {dict(counts)}")
    if by_type:
        print("by_product_type:")
        for ptype, counts in sorted(by_type.items()):
            print(f"  {ptype}: {dict(counts)}")
    if failures_list:
        print("failures:")
        for f in failures_list:
            print(f"  FAIL {f}")
    print("wrote reports/qa-metrics.json")


if __name__ == "__main__":
    main(sys.argv[1] if len(sys.argv) > 1 else "reports/junit.xml")
