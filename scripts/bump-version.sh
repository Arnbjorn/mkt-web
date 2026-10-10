#!/usr/bin/env bash
# Sets a new version number in style.css and adds a CHANGELOG section to fill in.
#   bash scripts/bump-version.sh 2.1.0
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

NEW="${1:-}"
if [[ ! "$NEW" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then echo "Användning: bash scripts/bump-version.sh X.Y.Z   (t.ex. 2.1.0)"; exit 1; fi
OLD="$(theme_version)"
[ "$NEW" = "$OLD" ] && { echo "Versionen är redan $NEW"; exit 1; }

# style.css (portable: write to a temp file instead of sed -i)
TMP="$(mktemp)"
sed "s/^Version:.*/Version:     $NEW/" "$THEME_DIR/style.css" > "$TMP" && mv "$TMP" "$THEME_DIR/style.css"

# CHANGELOG: new section directly under the intro
if ! grep -q "^## \[$NEW\]" "$ROOT/CHANGELOG.md"; then
  TMP="$(mktemp)"
  awk -v ver="$NEW" -v day="$(date +%F)" '
    !done && /^## \[/ { print "## [" ver "] — " day "\n\n### Nytt\n- \n\n### Ändrat\n- \n\n### Rättat\n- \n"; done=1 }
    { print }' "$ROOT/CHANGELOG.md" > "$TMP" && mv "$TMP" "$ROOT/CHANGELOG.md"
fi
echo "Version: $OLD → $NEW"
echo "Nästa steg: fyll i CHANGELOG.md, kör 'bash scripts/build.sh --with-tests', commit, och skapa taggen v$NEW."
