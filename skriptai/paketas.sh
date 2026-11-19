#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Masažo Gurmanai — temos archyvo paruošimas įkėlimui į svetainę.
#
#   ./skriptai/paketas.sh
#
# Sukuria dist/masazo-gurmanai-<versija>.zip. Archyvas įkeliamas:
#   Išvaizda → Temos → Pridėti temą → Įkelti temą
# (atnaujinant — WordPress pasiūlys „Pakeisti esamą įkeltąja“).
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail
cd "$(dirname "$0")/.."

THEME="masazo-gurmanai"
SRC="wp-content/themes/${THEME}"

# iCloud iškelti failai į archyvą patektų tušti — pirmiausia parsiunčiame.
find "$SRC" -type f -flags +dataless -print0 2>/dev/null | while IFS= read -r -d '' f; do cat "$f" > /dev/null; done

VERSION=$(grep -E '^Version:' "${SRC}/style.css" | head -1 | sed -E 's/^Version:[[:space:]]*//')
[ -n "$VERSION" ] || { echo "Nepavyko nustatyti versijos iš ${SRC}/style.css"; exit 1; }

# PHP sintaksės patikra (jei veikia vietinė aplinka) — kad į serverį nepatektų sugadintas failas.
if docker compose ps --status running --services 2>/dev/null | grep -qx wp; then
  ERR=$(docker compose exec -T wp sh -c "cd /var/www/html/wp-content/themes/${THEME} && find . -name '*.php' -exec php -l {} \; 2>&1 | grep -v '^No syntax errors' || true")
  [ -z "$ERR" ] || { echo "PHP klaidos:"; echo "$ERR"; exit 1; }
  echo "✓ PHP sintaksė tvarkinga"
fi

mkdir -p dist
OUT="dist/${THEME}-${VERSION}.zip"
rm -f "$OUT"
( cd wp-content/themes && zip -rqX "../../${OUT}" "${THEME}" -x '*.DS_Store' -x '*/_*' -x '*/.git*' )

echo "✓ ${OUT} ($(du -h "$OUT" | cut -f1 | tr -d ' '), $(unzip -l "$OUT" | tail -1 | awk '{print $2}') failai)"
