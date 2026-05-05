#!/bin/sh

read -p "Sicher? Deine Bib & Datenbank wird gelöscht! (y/n): " answer

if [[ "$answer" == "y" || "$answer" == "Y" ]]; then
	DIR="$HOME/.local/share/musikbruder"

	if [ ! -d $DIR ]; then
		echo -e "musikbruder/ existiert nicht."
		exit 1
	fi

	cd $DIR
	docker compose -f compose.deploy.yml down --volumes
	cd ..
	rm -rf musikbruder
else
	echo "Abgebrochen :)"
fi
