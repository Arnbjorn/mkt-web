#!/usr/bin/env bash
# Prints the CHANGELOG section for one version (used for the GitHub release notes).
#   bash scripts/changelog-section.sh 2.1.0
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/common.sh"
VER="${1:?version krävs, t.ex. 2.1.0}"
awk -v ver="$VER" '
  $0 ~ "^## \\[" ver "\\]" { grab=1; next }
  grab && /^## \[/        { exit }
  grab                    { print }' "$ROOT/CHANGELOG.md" | sed -e :a -e '/^\n*$/{$d;N;ba' -e '}'
