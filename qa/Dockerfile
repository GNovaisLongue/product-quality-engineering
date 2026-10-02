# syntax=docker/dockerfile:1
#
# Test-runner image for the Python/pytest suites (smoke, api, contract, db,
# integration, e2e). Ships Playwright + browsers pre-installed so `tests/e2e`
# doesn't need a manual `playwright install` step, and matches the pinned
# Playwright version previously vendored in the ad-hoc `qa/venv`.
FROM mcr.microsoft.com/playwright/python:v1.62.0-jammy

WORKDIR /app

COPY requirements-test.txt .
RUN pip install --no-cache-dir -r requirements-test.txt

COPY pytest.ini .
COPY qa/ ./qa
COPY tests/ ./tests
COPY openapi/ ./openapi

# Default: fast suites that don't require the frontend container.
# Override the command to target other suites, e.g.:
#   docker compose -f compose.yml -f compose.test.yml run --rm qa-test \
#       pytest tests/e2e -q
CMD ["pytest", "tests/smoke", "tests/api", "tests/contract", "tests/database", "tests/integration", "-q", "--junitxml=reports/junit.xml"]
