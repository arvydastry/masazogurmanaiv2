#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Masažo Gurmanai — vietinės WordPress + Elementor aplinkos paleidimas.
#
#   ./skriptai/paleisti.sh           paleidžia; pirmą kartą sudiegia WordPress, Elementor, temą ir turinį
#   ./skriptai/paleisti.sh --force   iš naujo įrašo pradinį puslapių turinį ir paslaugas
#                                    (vietiniai Elementor pakeitimai šiuose puslapiuose perrašomi!)
#   ./skriptai/paleisti.sh --reset   IŠTRINA vietinę duomenų bazę ir diegia nuo nulio
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail
cd "$(dirname "$0")/.."

C_OK=$'\033[32m'; C_W=$'\033[33m'; C_B=$'\033[1m'; C_0=$'\033[0m'
say()  { printf '%s\n' "${C_B}$*${C_0}"; }
ok()   { printf '%s\n' "${C_OK}✓${C_0} $*"; }
warn() { printf '%s\n' "${C_W}!${C_0} $*"; }

MODE="${1:-}"

# 1. Projektas guli ~/Documents — iCloud „Optimize Storage“ kartais iškelia failų turinį iš disko,
#    o Docker tokių failų nemato. Parsiunčiame juos atgal.
N=$(find . -type f -flags +dataless 2>/dev/null | wc -l | tr -d ' ')
if [ "${N:-0}" -gt 0 ]; then
  say "▸ iCloud iškėlė $N failų — parsiunčiama"
  find . -type f -flags +dataless -print0 2>/dev/null | while IFS= read -r -d '' f; do cat "$f" > /dev/null; done
  ok "failai diske"
fi

# 2. Nustatymų failas
if [ ! -f .env ]; then
  say "▸ Kuriamas .env (vietiniai slaptažodžiai sugeneruojami atsitiktinai)"
  gen() { LC_ALL=C tr -dc 'a-zA-Z0-9' </dev/urandom | head -c "$1"; }
  sed -e "s|^DB_PASSWORD=.*|DB_PASSWORD=$(gen 20)|" \
      -e "s|^DB_ROOT_PASSWORD=.*|DB_ROOT_PASSWORD=$(gen 20)|" \
      -e "s|^WP_ADMIN_PASSWORD=.*|WP_ADMIN_PASSWORD=$(gen 16)|" .env.example > .env
  ok ".env sukurtas"
fi
# shellcheck disable=SC1091
set -a; . ./.env; set +a

dc()  { docker compose "$@"; }
wpc() { docker compose exec -T cli wp --path=/var/www/html "$@" 2> >(grep -v '^Deprecated:' >&2); }

# 3. Docker
if ! docker info >/dev/null 2>&1; then
  say "▸ Paleidžiamas Docker Desktop"
  open -a Docker || true
  for _ in $(seq 1 90); do docker info >/dev/null 2>&1 && break; sleep 2; done
  docker info >/dev/null 2>&1 || { echo "Docker neveikia. Paleiskite Docker Desktop ir bandykite dar kartą."; exit 1; }
fi

if [ "$MODE" = "--reset" ]; then
  warn "Trinama vietinė duomenų bazė ir WordPress failai…"
  dc down -v
fi

say "▸ Paleidžiami konteineriai"
dc up -d
ok "konteineriai veikia"

say "▸ Laukiama, kol WordPress atsakys"
for i in $(seq 1 90); do
  CODE=$(curl -s -o /dev/null -m 5 -w '%{http_code}' "http://localhost:${WP_PORT}/" || true)
  [ "$CODE" != "000" ] && break
  sleep 2
  [ "$i" = 90 ] && { echo "WordPress neatsakė. Patikrinkite: docker compose logs wp"; exit 1; }
done
for _ in $(seq 1 60); do dc exec -T cli test -f /var/www/html/wp-config.php && break; sleep 2; done
ok "WordPress pasiekiamas: http://localhost:${WP_PORT}"

# 4. WordPress
if ! wpc core is-installed >/dev/null 2>&1; then
  say "▸ Diegiamas WordPress"
  wpc core install --url="${WP_URL}" --title="${WP_TITLE}" --admin_user="${WP_ADMIN_USER}" \
    --admin_password="${WP_ADMIN_PASSWORD}" --admin_email="${WP_ADMIN_EMAIL}" --skip-email >/dev/null
  wpc language core install lt_LT --activate >/dev/null 2>&1 || warn "nepavyko parsiųsti lt_LT (tęsiama angliškai)"
  wpc option update timezone_string "Europe/Vilnius" >/dev/null
  wpc rewrite structure '/%postname%' --hard >/dev/null 2>&1 || true
  # WordPress pavyzdinis turinys nereikalingas
  wpc post delete 1 2 --force >/dev/null 2>&1 || true
  ok "WordPress sudiegtas"
else
  ok "WordPress jau sudiegtas"
fi

# 5. Elementor ir tema
say "▸ Elementor ir tema"
wpc plugin is-installed elementor >/dev/null 2>&1 || wpc plugin install elementor >/dev/null
wpc plugin is-active elementor >/dev/null 2>&1 || wpc plugin activate elementor >/dev/null
ok "Elementor $(wpc plugin get elementor --field=version 2>/dev/null)"
[ "$(wpc theme list --status=active --field=name 2>/dev/null)" = "masazo-gurmanai" ] || wpc theme activate masazo-gurmanai >/dev/null
ok "tema „Masažo Gurmanai“ aktyvi"

# 6. Turinys (saugu kartoti: tai, kas jau sukurta ar redaguota, neperrašoma)
say "▸ Pradinis turinys"
if [ "$MODE" = "--force" ]; then
  wpc mg setup --force
else
  wpc mg setup
fi

echo
say "──────────────────────────────────────────────────────────────"
ok "Svetainė:         http://localhost:${WP_PORT}"
ok "Administravimas:  http://localhost:${WP_PORT}/wp-admin   (naudotojas: ${WP_ADMIN_USER}; slaptažodis — faile .env, eilutė WP_ADMIN_PASSWORD)"
say "──────────────────────────────────────────────────────────────"
echo "Stabdymas:        docker compose stop   (arba SUSTABDYTI.command; duomenys lieka)"
echo "Temos archyvas:   ./skriptai/paketas.sh"
