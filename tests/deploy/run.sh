#!/usr/bin/env bash
# Tests scripts/deploy.sh against a REAL SSH/SFTP server on this machine (localhost).
# Runs in GitHub Actions (CI job "Publiceringsskriptet"); needs Linux + sudo. Changes the
# machine (installs openssh-server, adds a user "web") — don't run it on your own computer.
#   bash tests/deploy/run.sh
set -u
[ "$(uname -s)" = "Linux" ] || { echo "Bara Linux (körs i GitHub Actions)."; exit 0; }
SUDO=""; [ "$(id -u)" = 0 ] || SUDO="sudo"
REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
export DEBIAN_FRONTEND=noninteractive
$SUDO apt-get update -qq >/dev/null && $SUDO apt-get install -y -qq openssh-server lftp curl zip unzip python3 >/dev/null 2>&1
$SUDO mkdir -p /run/sshd
# sshd uses the FIRST value it finds, and cloud images turn passwords off in sshd_config.d — so go first.
$SUDO mkdir -p /etc/ssh/sshd_config.d
echo 'PasswordAuthentication yes' | $SUDO tee /etc/ssh/sshd_config.d/00-deploytest.conf >/dev/null
$SUDO sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config
if pidof sshd >/dev/null; then $SUDO systemctl restart ssh 2>/dev/null || $SUDO pkill -HUP sshd; else $SUDO /usr/sbin/sshd; fi
sleep 1
$SUDO useradd -m web 2>/dev/null; echo 'web:p@ss "w0rd" $x' | $SUDO chpasswd
WP=/home/web/www/wp-content/themes
$SUDO mkdir -p $WP/twentytwentyfive && $SUDO chown -R web:web /home/web/www && $SUDO chmod -R o+rX /home/web
SITE="$(mktemp -d)"; echo '<link href="/wp-content/themes/tandstiftet-theme-v2/style.css">' > "$SITE/index.html"
(cd "$SITE" && python3 -m http.server 8080 >/dev/null 2>&1 &)
sleep 1

WORKDIR="$(mktemp -d)"; cp -r "$REPO/." "$WORKDIR/" && cd "$WORKDIR"
mkdir -p dist && (cd theme && zip -qr ../dist/tandstiftet-theme-v2-test.zip tandstiftet-theme-v2)
export CI=true SFTP_HOST=localhost SFTP_USER=web THEMES_DIR=$WP SITE_URL=http://localhost:8080/
pass=0; fail=0
t() { if "$@"; then echo "PASS"; pass=$((pass+1)); else echo "FAIL"; fail=$((fail+1)); fi; }

echo "=== 1. testa-anslutning med lösenord (med citattecken och \$ i) ==="
t env SFTP_PASSWORD='p@ss "w0rd" $x' bash scripts/deploy.sh check
echo "=== 2. fel lösenord ger tydligt fel ==="
t bash -c '! env SFTP_PASSWORD=fel bash scripts/deploy.sh check'
echo "=== 3. fel sökväg vägras ==="
t bash -c '! env SFTP_PASSWORD="p@ss \"w0rd\" \$x" THEMES_DIR=/home/web bash scripts/deploy.sh check'
echo "=== 4. första publiceringen ==="
t env SFTP_PASSWORD='p@ss "w0rd" $x' bash scripts/deploy.sh publish dist/tandstiftet-theme-v2-test.zip
t test -f $WP/tandstiftet-theme-v2/style.css
t test -d $WP/twentytwentyfive

echo "=== 5. andra publiceringen med SSH-nyckel, förra sparas som .old ==="
export K="$(mktemp -d)/k"; ssh-keygen -q -t ed25519 -N '' -f "$K" && $SUDO mkdir -p /home/web/.ssh && $SUDO cp "$K.pub" /home/web/.ssh/authorized_keys && $SUDO chown -R web:web /home/web/.ssh && $SUDO chmod 700 /home/web/.ssh
echo "marker-v1" | $SUDO tee $WP/tandstiftet-theme-v2/MARKER >/dev/null
t env SFTP_KEY="$(cat $K)" SFTP_KNOWN_HOSTS="$(ssh-keyscan localhost 2>/dev/null)" bash scripts/deploy.sh publish dist/tandstiftet-theme-v2-test.zip
t test -f $WP/tandstiftet-theme-v2.old/MARKER
t test ! -f $WP/tandstiftet-theme-v2/MARKER
t test ! -e $WP/tandstiftet-theme-v2.new

echo "=== 6. trasig sajt efter publicering → backar automatiskt ==="
echo "marker-good" | $SUDO tee $WP/tandstiftet-theme-v2/MARKER >/dev/null
t bash -c '! env SFTP_PASSWORD="p@ss \"w0rd\" \$x" SITE_URL=http://localhost:8080/finns-inte bash scripts/deploy.sh publish dist/tandstiftet-theme-v2-test.zip'
t grep -q marker-good $WP/tandstiftet-theme-v2/MARKER

echo "=== 7. fel fingeravtryck vägras ==="
t bash -c '! env SFTP_KEY="$(cat $K)" SFTP_KNOWN_HOSTS="localhost ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOMqqnkVzrm0SdG6UOoqKLsabgH5C9okWi0dh2l9GKJl" bash scripts/deploy.sh check'

echo; echo "RESULTAT: $pass godkända, $fail misslyckade"
ls -la $WP
[ "$fail" -eq 0 ]
