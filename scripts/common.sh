#!/usr/bin/env bash
# Shared helpers — sourced by the other scripts, not run directly.
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
# shellcheck source=config.sh
source "$ROOT/scripts/config.sh"
THEME_DIR="$ROOT/theme/$THEME_SLUG"
DIST_DIR="$ROOT/dist"

ok()   { printf '  \033[32m✓\033[0m %s\n' "$*"; }
bad()  { printf '  \033[31m✗\033[0m %s\n' "$*"; FAILED=1; }
warn() { printf '  \033[33m!\033[0m %s\n' "$*"; }
FAILED=0

# In CI a missing tool is an error; on a laptop it is only a warning.
need_tool() {
  local tool="$1" why="$2"
  if command -v "$tool" >/dev/null 2>&1; then return 0; fi
  if [ "${CI:-}" = "true" ]; then bad "$tool saknas ($why)"; else warn "$tool saknas — hoppar över: $why"; fi
  return 1
}

theme_version() {
  sed -n 's/^Version:[[:space:]]*//p' "$THEME_DIR/style.css" | head -1 | tr -d '\r '
}
