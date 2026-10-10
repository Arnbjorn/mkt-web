#!/usr/bin/env bash
# Static checks on the theme. Fast (a few seconds) — run before every commit.
#   bash scripts/check.sh
set -uo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

echo "Kontrollerar temat: $THEME_SLUG"

# 1. The theme folder and its header ------------------------------------------
if [ ! -f "$THEME_DIR/style.css" ]; then bad "hittar inte $THEME_DIR/style.css"; exit 1; fi
VERSION="$(theme_version)"
if [[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then ok "version $VERSION (style.css)"; else bad "Version i style.css är inte x.y.z (fick: '$VERSION')"; fi
grep -q '^Theme Name:' "$THEME_DIR/style.css"        && ok "Theme Name finns"          || bad "Theme Name saknas i style.css"
grep -q '^Text Domain:[[:space:]]*tandstiftet' "$THEME_DIR/style.css" && ok "Text Domain: tandstiftet" || bad "Text Domain måste vara 'tandstiftet' (importen av inställningar letar efter den)"
for f in functions.php header.php footer.php front-page.php index.php; do
  [ -f "$THEME_DIR/$f" ] || bad "obligatorisk fil saknas: $f"
done

# 2. Odd file names (an earlier brace-expansion accident left a folder named '{inc,template-parts') ---
ODD="$(find "$THEME_DIR" \( -name '*{*' -o -name '*}*' -o -name '*,*' -o -name '* *' \) -print)"
if [ -n "$ODD" ]; then bad "konstiga fil-/mappnamn:"; echo "$ODD" | sed 's/^/      /'; else ok "inga konstiga fil-/mappnamn"; fi

# 3. PHP syntax ------------------------------------------------------------
if need_tool php "PHP-syntaxkontroll"; then
  COUNT=0
  while IFS= read -r -d '' f; do
    if ! php -l "$f" >/dev/null 2>&1; then bad "PHP-fel i ${f#$ROOT/}"; php -l "$f" 2>&1 | sed 's/^/      /'; fi
    COUNT=$((COUNT+1))
  done < <(find "$THEME_DIR" -name '*.php' -print0)
  [ "$FAILED" -eq 0 ] && ok "PHP-syntax OK ($COUNT filer)"
fi

# 4. JavaScript syntax -------------------------------------------------------
if need_tool node "JavaScript-syntaxkontroll"; then
  JS=0
  while IFS= read -r -d '' f; do
    node --check "$f" 2>/dev/null || { bad "JS-fel i ${f#$ROOT/}"; node --check "$f" 2>&1 | sed 's/^/      /'; }
    JS=$((JS+1))
  done < <(find "$THEME_DIR" -name '*.js' -print0)
  ok "JavaScript-syntax OK ($JS filer)"
fi

# 5. CSS: balanced braces (catches a lost '}' that would silently break everything below it) ---
while IFS= read -r -d '' f; do
  OPEN=$(tr -cd '{' < "$f" | wc -c | tr -d ' '); CLOSE=$(tr -cd '}' < "$f" | wc -c | tr -d ' ')
  if [ "$OPEN" = "$CLOSE" ]; then ok "CSS-parenteser balanserade i ${f#$THEME_DIR/} ($OPEN)"; else bad "CSS-parenteser i ${f#$THEME_DIR/}: $OPEN öppnar, $CLOSE stänger"; fi
done < <(find "$THEME_DIR" -name '*.css' -print0)

# 6. The changelog knows about this version ------------------------------------
if [ -f "$ROOT/CHANGELOG.md" ] && grep -q "^## \[$VERSION\]" "$ROOT/CHANGELOG.md"; then ok "CHANGELOG.md har ett avsnitt för $VERSION"; else bad "CHANGELOG.md saknar '## [$VERSION]'"; fi

echo
if [ "$FAILED" -eq 0 ]; then echo "Alla kontroller OK."; else echo "Något misslyckades — se ✗ ovan."; fi
exit "$FAILED"
