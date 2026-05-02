#!/bin/sh

# [ -d "$HOME/.local/share" ] || {
#   echo "~/.local/share gibt's nicht!"
#   exit 1
# }

PINK="\033[38;5;205m"
GREEN="\033[32m"
YELLOW="\033[33m"
LILA="\033[35m"
GREY="\033[2;37m"
RED="\033[31m"
NOCO="\033[0m"

# Run a command completly silent.
silent() {
	"$@" >/dev/null 2>&1
}

# Colored echo.
cecho() {
	color=$1
	shift
	echo -e "${color}$@${NOCO}"
}

# Colored inline echo.
cnecho() {
	color=$1
	shift
	echo -en "${color}$@${NOCO}"
}

# Colored, indented echo.
ciecho() {
	color=$1
	shift
	echo -e "$GREY╟$NOCO  ${color}$@${NOCO}"
}

HOME_DIR=${XDG_DATA_HOME:-$HOME}
WORK_DIR="$HOME_DIR/.local/share"
BIN_DIR="$HOME_DIR/.local/bin"

echo -e "\n$PINK/////   //////   //      ////    /////  //////"
sleep 0.05
echo -e "//  //  //   //  //  //  //  //  //     //   //"
sleep 0.05
echo -e "/////   /////    //  //  //  //  ////   /////  "
sleep 0.05
echo -e "//  //  //  //   //  //  //  //  //     //  // "
sleep 0.05
echo -e "////    //   //   ////   ////    /////  //   //"
sleep 0.05
echo -e "Dein Bruder der Musik (I use Arch btw)$NOCO\n"
sleep 0.05

read -p "⌨️  Hast du Bock? ja/* " you_in </dev/tty

if [ "$you_in" != "ja" ]; then
	cecho "$YELLOW" "✋ Alles klar Bruder, vielleicht ein andermal!"
	exit 1
fi

cecho "$GREEN" "😍 Nice, dann las los gehen jetzt…"

# Ensure the .local/share dir exists by creating it. This won't
# output anything if it already exists.
mkdir -p "$WORK_DIR"
mkdir -p "$BIN_DIR"

# + Check dependencies.
sleep 0.2
cnecho "$GREY" "·"

# ! PHP installed?
command -v php >/dev/null 2>&1 || {
	echo -e "\n$YELLOW💔 Sorry Bruder, aber du musst PHP installieren. Das findest du auf https://www.php.net/manual/en/install.unix.php - Versuch es danach nochmal!$NOCO"
	exit 1
}

sleep 0.2
cnecho "$GREY" "·"

# ! tput (ncurses) installed?
command -v tput >/dev/null 2>&1 || {
	echo -e "\n$GREY ncurses ist nicht installiert, Terminal-Output sieht nicht so schön aus aber wird funktionieren.$NOCO"
}

sleep 0.2
cnecho "$GREY" "·"

# + Install composer if not exists
if ! command -v composer >/dev/null 2>&1; then
	echo -e "\n$YELLOW♻️  Installiere composer in »~/.local/bin«$NOCO"

	curl -sS https://getcomposer.org/installer | php
	echo -e "Moving composer to »~/.local/bin«"
	mv composer.phar $BIN_DIR/composer
	echo -e "Good went!"
fi

sleep 0.2
cnecho "$GREY" "·"
sleep 0.2
echo -n ✅

# Set working dir.
cd $WORK_DIR

# echo -en "\n🍎"
# sleep 0.2
# echo -n "🍊"
# sleep 0.2
# echo -n "🍉"
# sleep 0.2
# echo -n "🍇"
# sleep 0.2
# echo -n "🍋"
# sleep 0.2
# echo -n "🍑"
# sleep 0.2
# echo -n "🥭"
# sleep 0.2
# echo -n "🥐"
# sleep 0.2
# echo -n "🧀"
# sleep 0.2
# echo -n "🍓"
# sleep 0.2
# echo -n "."
# sleep 0.2
# echo -n "."
# sleep 0.2
# echo -n "."
# sleep 0.2
# echo -en "🎌\n"
# sleep 0.2

# TODO: Check for .local being available.

# + Clone GitHub
echo -e "\n"
sleep 0.2
cecho "$LILA" "App runterladen…"

# If the base directory already exists, ask for reinstalling everything.
if [ -d "musikbruder" ]; then
	ciecho "$RED" "⬇️  »~/.local/share/musikbruder« existiert schon!"
	read -p "$(echo -e "${GREY}╟${NOCO}  ⌨️  Alles löschen & neu installieren? ja/* ")" delete </dev/tty

	if [ "$delete" != "ja" ]; then
		ciecho "$GREEN" "➡️  Alles klar Bruder, vielleicht ein andermal!"
		exit 1
	else
		ciecho "$GREY" "Oha, wird gelöscht…"
		cd musikbruder
		docker compose -f compose.deploy.yml down --volumes >/dev/null 2>&1
		cd ..
		rm -rf musikbruder
	fi
fi

ciecho "$GREY" "Kloniere GitHub Repo…"
git clone https://github.com/brudermusscode/UnSpotify.git musikbruder >/dev/null 2>&1
cd musikbruder

# + Link Music Directory
MUSIC_DIR="$HOME/Music"
MUSIC_DIR_LINKED=false

if [ -d $MUSIC_DIR ]; then
	read -p "$(echo -e "${GREY}╟${NOCO}  ⌨️  Willst du deinen lokalen Musik-Ordner verlinken? ja/* ")" link_music </dev/tty

	if [ "$link_music" == "ja" ]; then
		REPLACEMENT="- $MUSIC_DIR:/data/public/data/user/1/tracks"
		sed -i "s|%MUSIC_DIR_AS_VOLUME%|${REPLACEMENT}|g" compose-dummy
		MUSIC_DIR_LINKED=true
	else
		sed -i "s|%MUSIC_DIR_AS_VOLUME%|""|g" compose-dummy
	fi

	mv compose-dummy compose.deploy.yml
fi

echo -n "✅"

# + Dependency directories and files.
echo -e "\n"
cecho "$LILA" "Alle Relevanzen erstellen…"
ciecho "$GREY" "Mach ich Bruder, warte kurz…"

mkdir -p public/data/user/1/{tracks,art,portraits,videos}
mkdir -p public/data/user/1/tracks/deleted
mkdir -p storage/logs
chmod a+rw -R storage public sql
touch sql/last_migration
chmod a+rw sql/last_migration
cp .env.example .env

echo -n "✅"

# + Composer
echo -e "\n"
cecho "$LILA" "Composer?"
ciecho "$GREY" "Ist wichtig, versprochen…"
composer install >/dev/null 2>&1
composer dump-autoload >/dev/null 2>&1
echo -n "✅"

# + Build docker-container.
echo -e "\n"
cecho "$LILA" "Bruder bauen…"
ciecho "$GREY" "Lehn dich zurück, das kann 1 bisschen dauern 😇…"
docker compose -f compose.deploy.yml build >/dev/null 2>&1
echo -n "✅"

# + Start the app.
echo -e "\n"
cecho "$LILA" "Endspurt…"
ciecho "$GREY" "Ist gleich fertig…"
docker compose -f compose.deploy.yml up -d >/dev/null 2>&1

URL="http://localhost:6789"

if command -v xdg-open >/dev/null 2>&1; then
	xdg-open "$URL"
else
	ciecho "$GREY" "xdg-open fehlt leider, sont hätte sich das Fenster nun automatisch geöffnet 🥸"
fi
echo -n "✅"

# # DONE
echo -e "\n"
if $MUSIC_DIR_LINKED; then
	cecho "$GREEN" "🤝 Fertig! Geh zu $URL - Musik wird automatisch aus deinem lokalen Musik-Ordner synchronisiert!"
else
	cecho "$GREEN" "🤝 Fertig! Geh zu $URL ❤️"
fi
