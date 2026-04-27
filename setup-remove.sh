#!/bin/sh

read -p "Sicher? Deine Bib & Datenbank wird gelöscht! (y/n): " answer

if [[ "$answer" == "y" || "$answer" == "Y" ]]; then
  docker compose -f compose.deploy.yml down --volumes

  rm -rf vendor
  rm -rf node_modules
  rm -rf public/data
  rm -rf storage
  rm -rf .env
  rm -rf sql/last_migration
else
    echo "Abgebrochen :)"
fi
