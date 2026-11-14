#!/usr/bin/env bash
set -e

echo "=========================================="
echo " Kostara Automated CI Verification Suite"
echo "=========================================="

echo ""
echo "[Step 1/3] Checking Code Style with Laravel Pint..."
vendor/bin/pint --test
echo "✓ Laravel Pint passed."

echo ""
echo "[Step 2/3] Executing Automated PHPUnit Feature Tests..."
php artisan test --compact
echo "✓ Backend test suite passed."

echo ""
echo "[Step 3/3] Building Production Frontend Assets (Vite)..."
(cd frontend && npm run build)
echo "✓ Frontend build passed."

echo ""
echo "=========================================="
echo " ALL CI CHECKS PASSED SUCCESSFULLY (100%)"
echo "=========================================="
