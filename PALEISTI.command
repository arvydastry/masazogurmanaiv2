#!/bin/bash
# Masažo Gurmanai — vietinė svetainė: dukart spustelėkite šį failą.
# Parsiunčia iš iCloud iškeltus failus, prireikus paleidžia Docker Desktop, įjungia svetainę ir atidaro ją naršyklėje.
cd "$(dirname "$0")" || exit 1
./skriptai/paleisti.sh || { echo; read -r -p "Nepavyko (žr. klaidą aukščiau). Enter — uždaryti"; exit 1; }
PORT="$(grep -E '^WP_PORT=' .env | cut -d= -f2-)"
open "http://localhost:${PORT:-8170}/"
echo; echo "Išjungti: dukart spustelėkite SUSTABDYTI.command."
