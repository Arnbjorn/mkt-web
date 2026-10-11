#!/usr/bin/env bash
# Publishes the theme to the web host (one.com) over SFTP.
# Used by .github/workflows/deploy.yml — see docs/PUBLICERA.md for the setup.
#
#   bash scripts/deploy.sh check                 # only test the connection and the folder
#   bash scripts/deploy.sh publish <theme.zip>   # upload, swap in, check the site, roll back on error
#
# Settings come from environment variables (GitHub Secrets in the workflow — never from the repo):
#   SFTP_HOST, SFTP_USER          server and user name (one.com control panel → SSH & SFTP)
#   SFTP_PASSWORD or SFTP_KEY     password, or a private SSH key (the whole key text)
#   SFTP_PORT                     optional, default 22
#   SFTP_KNOWN_HOSTS              optional: the server's fingerprint line(s); otherwise fetched on the fly
#   THEMES_DIR                    the folder that holds the themes, must end in /wp-content/themes
#   SITE_URL                      optional: page checked after publishing (rolls back if it is broken)
#
# Safety: only the folders <slug>, <slug>.new and <slug>.old inside THEMES_DIR are ever touched.
# The previous version is kept as <slug>.old until the next publish.
set -euo pipefail
source "$(dirname "${BASH_SOURCE[0]}")/common.sh"

MODE="${1:-}"; ZIP="${2:-}"
die() { bad "$*"; exit 1; }
[ "$MODE" = "check" ] || [ "$MODE" = "publish" ] || die "Använd: deploy.sh check | publish <tema.zip>"

for v in SFTP_HOST SFTP_USER THEMES_DIR; do [ -n "${!v:-}" ] || die "$v saknas (lägg in den som GitHub Secret)"; done
[ -n "${SFTP_PASSWORD:-}${SFTP_KEY:-}" ] || die "SFTP_PASSWORD eller SFTP_KEY saknas (lägg in en av dem som GitHub Secret)"
THEMES_DIR="${THEMES_DIR%/}"
case "$THEMES_DIR" in
  */wp-content/themes) ;;
  */wp-content/themes/*) die "THEMES_DIR ska sluta på /wp-content/themes — ta bort det som står efter (t.ex. /$THEME_SLUG). Det är mappen som INNEHÅLLER teman som ska anges.";;
  *) die "THEMES_DIR måste sluta på /wp-content/themes";;
esac
command -v lftp >/dev/null || die "lftp saknas"
PORT="${SFTP_PORT:-22}"

# ── SSH settings: known host key + key or password ──
WORK="$(mktemp -d)"; trap 'rm -rf "$WORK"' EXIT
KNOWN="$WORK/known_hosts"
if [ -n "${SFTP_KNOWN_HOSTS:-}" ]; then
  printf '%s\n' "$SFTP_KNOWN_HOSTS" > "$KNOWN"
else
  warn "SFTP_KNOWN_HOSTS saknas — hämtar serverns fingeravtryck nu (lägg gärna in det som Secret, se docs/PUBLICERA.md)"
  ssh-keyscan -p "$PORT" -T 15 "$SFTP_HOST" 2>/dev/null > "$KNOWN" || true
  [ -s "$KNOWN" ] || die "kunde inte nå $SFTP_HOST på port $PORT"
fi
SSH_CMD="ssh -a -x -p $PORT -o StrictHostKeyChecking=yes -o UserKnownHostsFile=$KNOWN"
if [ -n "${SFTP_KEY:-}" ]; then
  printf '%s\n' "$SFTP_KEY" | tr -d '\r' > "$WORK/key"; chmod 600 "$WORK/key"
  SSH_CMD="$SSH_CMD -i $WORK/key -o IdentitiesOnly=yes -o PasswordAuthentication=no"
  OPEN_LINE="$(printf 'open -u "%s," -p %s sftp://%s' "$SFTP_USER" "$PORT" "$SFTP_HOST")"
else
  export LFTP_PASSWORD="$SFTP_PASSWORD"   # read by lftp itself — never written into a file or command line
  OPEN_LINE="$(printf 'open --env-password -u "%s" -p %s sftp://%s' "$SFTP_USER" "$PORT" "$SFTP_HOST")"
fi

# Runs lftp commands (one per line on stdin) on the server. Stops at the first error.
remote() {
  { printf 'set cmd:fail-exit yes\nset net:max-retries 2\nset net:timeout 30\nset sftp:auto-confirm no\n'
    printf 'set sftp:connect-program "%s"\n' "$SSH_CMD"
    printf '%s\n' "$OPEN_LINE"
    cat; } > "$WORK/cmds"
  lftp -f "$WORK/cmds"
}
exists() { printf 'cls -d "%s"\n' "$1" | remote >/dev/null 2>&1; }

SLUG="$THEME_SLUG"; LIVE="$THEMES_DIR/$SLUG"; NEW="$LIVE.new"; OLD="$LIVE.old"

echo "── Anslutning ──"
printf 'cd "%s"\ncls -1\n' "$THEMES_DIR" | remote > "$WORK/list" || die "kom inte in i $THEMES_DIR (fel användare, lösenord/nyckel eller sökväg?)"
ok "inloggad på $SFTP_HOST, mappen finns: $THEMES_DIR"
echo "    teman där: $(tr '\n' ' ' < "$WORK/list" | sed 's#/##g')"
if grep -qx "$SLUG/\?" "$WORK/list"; then ok "$SLUG finns redan där (ersätts vid publicering)"; else warn "$SLUG finns inte där än — första publiceringen skapar den"; fi
[ "$MODE" = "check" ] && { echo; echo "Anslutningen fungerar. Inget har ändrats."; exit 0; }

# ── Publish ──
[ -f "$ZIP" ] || die "hittar inte zip-filen: $ZIP"
echo; echo "── Publicerar $(basename "$ZIP") ──"
unzip -q "$ZIP" -d "$WORK/unzipped"
[ -f "$WORK/unzipped/$SLUG/style.css" ] || die "zip-filen innehåller inte $SLUG/style.css"
VERSION="$(sed -n 's/^Version:[[:space:]]*//p' "$WORK/unzipped/$SLUG/style.css" | head -1 | tr -d '\r ')"

# 1. upload to <slug>.new (the site keeps using the old version meanwhile)
printf 'rm -r -f "%s"\nmirror -R --no-perms --parallel=4 "%s" "%s"\n' "$NEW" "$WORK/unzipped/$SLUG" "$NEW" | remote \
  || die "uppladdningen misslyckades — sajten är orörd"
ok "uppladdat till $SLUG.new"

# 2. swap: current → .old, .new → current (two quick renames)
HAD_LIVE=0; exists "$LIVE" && HAD_LIVE=1
{ printf 'rm -r -f "%s"\n' "$OLD"; [ "$HAD_LIVE" = 1 ] && printf 'mv "%s" "%s"\n' "$LIVE" "$OLD"; printf 'mv "%s" "%s"\n' "$NEW" "$LIVE"; } | remote \
  || die "bytet misslyckades — kontrollera mappen $THEMES_DIR (förra versionen ligger i $SLUG.old)"
ok "version $VERSION är på plats (förra versionen sparad som $SLUG.old)"

rollback() {
  [ "$HAD_LIVE" = 1 ] || { bad "kan inte backa: det fanns ingen tidigare version"; return; }
  printf 'rm -r -f "%s"\nmv "%s" "%s"\nmv "%s" "%s"\n' "$NEW" "$LIVE" "$NEW" "$OLD" "$LIVE" | remote \
    && warn "backade till förra versionen (den trasiga ligger i $SLUG.new)" \
    || bad "kunde inte backa automatiskt — byt namn på $SLUG.old till $SLUG via SFTP"
}

# 3. is the site still alive?
if [ -n "${SITE_URL:-}" ]; then
  sleep 3
  CODE="$(curl -sS -L -o "$WORK/page.html" -w '%{http_code}' --max-time 30 -H 'Cache-Control: no-cache' "$SITE_URL?tmc_deploy=$(date +%s)" || echo 000)"
  if [ "$CODE" != "200" ] || grep -qiE 'critical error|kritiskt fel|Fatal error|Parse error' "$WORK/page.html"; then
    bad "sajten svarar inte som den ska efter publiceringen (HTTP $CODE)"
    rollback; exit 1
  fi
  ok "$SITE_URL svarar (HTTP 200, inget PHP-fel)"
  grep -q "$SLUG" "$WORK/page.html" || warn "sidan nämner inte $SLUG — är temat aktiverat på sajten? (eller visar cachen en gammal sida)"
fi
echo; echo "Klart: $SLUG $VERSION publicerat. Rensa one.com-cachen i wp-admin om sidan ser gammal ut."
