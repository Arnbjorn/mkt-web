#!/usr/bin/env bash
# Runs the automated tests.
#   bash scripts/test.sh              # everything that can run on this machine
#   bash scripts/test.sh --no-browser # skip the (slower) real-browser tests
set -uo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/common.sh"
SKIP_BROWSER=0; [ "${1:-}" = "--no-browser" ] && SKIP_BROWSER=1

echo "── PHP-tester (logik i functions.php) ──"
if need_tool php "PHP-tester"; then php "$ROOT/tests/php/run.php" || FAILED=1; fi

echo; echo "── CSS-tester (kontrast och minsta textstorlek) ──"
if need_tool python3 "CSS-tester"; then python3 "$ROOT/tests/css/run.py" || FAILED=1; fi

echo; echo "── Webbläsartester (bredder, meny, header) ──"
if [ "$SKIP_BROWSER" -eq 1 ]; then
  warn "hoppar över webbläsartester (--no-browser)"
elif command -v python3 >/dev/null 2>&1 && python3 -c "import playwright" >/dev/null 2>&1; then
  python3 "$ROOT/tests/browser/run.py" || FAILED=1
else
  if [ "${CI:-}" = "true" ]; then bad "playwright saknas"; else warn "playwright är inte installerat — se README (avsnittet 'Köra testerna') — hoppar över"; fi
fi

echo
if [ "$FAILED" -eq 0 ]; then echo "Alla tester OK."; else echo "Något misslyckades."; fi
exit "$FAILED"
