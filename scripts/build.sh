#!/usr/bin/env bash
# Builds the installable theme package.
#   bash scripts/build.sh               # checks, then dist/<slug>-<version>.zip
#   bash scripts/build.sh --with-tests  # also runs the full test suite first
#
# The zip contains ONE top-level folder named exactly THEME_SLUG, which is what
# WordPress (Utseende → Teman → Lägg till nytt → Ladda upp tema) expects.
set -uo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

bash "$ROOT/scripts/check.sh" || { echo "Bygget avbröts: kontrollerna misslyckades."; exit 1; }
if [ "${1:-}" = "--with-tests" ]; then
  bash "$ROOT/scripts/test.sh" || { echo "Bygget avbröts: testerna misslyckades."; exit 1; }
fi
need_tool zip "att packa temat" || { echo "Installera 'zip' och försök igen."; exit 1; }

VERSION="$(theme_version)"
STAGE="$(mktemp -d)"; trap 'rm -rf "$STAGE"' EXIT
cp -R "$THEME_DIR" "$STAGE/$THEME_SLUG"

# keep development leftovers out of the package
find "$STAGE" \( -name '.DS_Store' -o -name 'Thumbs.db' -o -name '*.orig' -o -name '*.rej' -o -name '*~' -o -name '*.map' -o -name '.gitkeep' \) -delete

mkdir -p "$DIST_DIR"
OUT="$DIST_DIR/${THEME_SLUG}-${VERSION}.zip"
rm -f "$OUT"
# sorted file list + -X (no extra file attributes) so the same source gives the same file order
( cd "$STAGE" && find "$THEME_SLUG" -type f | LC_ALL=C sort | zip -X -q "$OUT" -@ )

# verify what we made
TOP="$(unzip -Z1 "$OUT" | cut -d/ -f1 | sort -u)"
if [ "$TOP" != "$THEME_SLUG" ]; then echo "✗ zip:en har fel rotmapp: '$TOP' (ska vara '$THEME_SLUG')"; exit 1; fi
unzip -Z1 "$OUT" | grep -qx "$THEME_SLUG/style.css" || { echo "✗ style.css saknas i zip:en"; exit 1; }

FILES="$(unzip -Z1 "$OUT" | wc -l | tr -d ' ')"
SIZE="$(du -h "$OUT" | cut -f1)"
echo
echo "Byggde ${OUT#$ROOT/}"
echo "  version: $VERSION   filer: $FILES   storlek: $SIZE"
if command -v sha256sum >/dev/null 2>&1; then echo "  sha256:  $(sha256sum "$OUT" | cut -d' ' -f1)"; elif command -v shasum >/dev/null 2>&1; then echo "  sha256:  $(shasum -a 256 "$OUT" | cut -d' ' -f1)"; fi
