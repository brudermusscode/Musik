#!/bin/sh

# Create assets file structure.
echo "[+] Erstmal alle Ordner erstellen …"
mkdir -p public/data/user/1/{tracks,art,portraits,videos}
mkdir -p storage/logs
chmod a+rw -R storage public sql
cp .env.example .env
touch sql/last_migration

if ! command -v composer >/dev/null 2>&1; then
    echo "[+] Installiere composer in »~/.local/bin« …"

    curl -sS https://getcomposer.org/installer | php
    mv composer.phar .local/bin/composer
fi

echo "[+] Jetzt composer Relevanzen …"
composer install
composer dump-autoload

docker compose -f compose.deploy.yml up -d

echo "[!] Fertig! Geh zu http://localhost:6789"