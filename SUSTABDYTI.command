#!/bin/bash
# Masažo Gurmanai — vietinės svetainės išjungimas (duomenys lieka).
cd "$(dirname "$0")" || exit 1
docker compose stop && echo "Vietinė svetainė išjungta."
