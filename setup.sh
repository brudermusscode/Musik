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

section_end() {
	echo -n "✅"
	echo -e "\n"
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

read -p "Hast du Bock? Schreib [ja] oder [nein] ⌨️  " you_in </dev/tty

if [ "$you_in" != "ja" ]; then
	cecho "$YELLOW" "✋ Alles klar Bruder, vielleicht ein andermal!"
	exit 1
fi

cecho "$GREEN" "😍 Nice, dann las los gehen jetzt…"

# Ensure the .local/share dir exists by creating it. This won't
# output anything if it already exists.
mkdir -p "$WORK_DIR"
mkdir -p "$BIN_DIR"

echo -e ""

# + Check dependencies.
# ! PHP installed?
command -v php >/dev/null 2>&1 || {
	echo -e "\n$YELLOW💔 Sorry Bruder, aber du musst PHP installieren. Das findest du auf https://www.php.net/manual/en/install.unix.php - Versuch es danach nochmal!$NOCO"
	exit 1
}

# + Install composer
if command -v composer >/dev/null 2>&1; then
	cecho "$LILA" "Dependencies!"
	ciecho "$GREY" "Installiere composer…"
	curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1
	ciecho "$GREY" "In »~/.local/bin« verschieben…"
	mv composer.phar $BIN_DIR/composer
fi

section_end

# Set working dir.
cd $WORK_DIR

# TODO: Check for .local being available.

# + Clone GitHub
sleep 0.2
cecho "$LILA" "App runterladen…"

# If the base directory already exists, ask for reinstalling everything.
if [ -d "musikbruder" ]; then
	ciecho "$RED" "»~/.local/share/musikbruder« existiert schon!"
	read -p "$(echo -e "${GREY}╟${NOCO}  Alles löschen & neu installieren? [ja]/[nein] ⌨️  ")" delete </dev/tty

	if [ "$delete" != "ja" ]; then
		ciecho "$GREEN" "Alles klar Bruder, vielleicht ein andermal!"
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

section_end

# + Dependency directories and files.
cecho "$LILA" "Alle Relevanzen erstellen…"
ciecho "$GREY" "Mach ich Bruder, warte kurz…"
ciecho "$GREY" "Ordner erstellen…"
mkdir -p public/data/user/1/{tracks,art,portraits,videos}
mkdir -p public/data/user/1/tracks/{local,deleted}
mkdir -p storage/logs
touch sql/last_migration
ciecho "$GREY" "Rechte einstellen…"
chmod a+rw -R storage public sql
chmod a+rw sql/last_migration
ciecho "$GREY" "Environment erstellen…"
cp .env.example .env

# + Link Music Directory
MUSIC_DIR="$HOME/Music"
MUSIC_DIR_LINKED=false

if [ -d $MUSIC_DIR ]; then
	read -p "$(echo -e "${GREY}╟${NOCO}  Willst du deinen lokalen Musik-Ordner synchronisieren? [ja]/[nein] ⌨️  ")" link_music </dev/tty

	if [ "$link_music" == "ja" ]; then
		REPLACEMENT="- $MUSIC_DIR:/data/public/data/user/1/tracks/local"
		sed -i "s|%MUSIC_DIR_AS_VOLUME%|${REPLACEMENT}|g" compose-dummy
		MUSIC_DIR_LINKED=true
	else
		sed -i "s|%MUSIC_DIR_AS_VOLUME%|""|g" compose-dummy
	fi

	mv compose-dummy compose.deploy.yml
fi

section_end

# + Composer
cecho "$LILA" "Composer?"
ciecho "$GREY" "Ist wichtig, versprochen…"
composer install >/dev/null 2>&1
ciecho "$GREY" "Alle Relevanzen dumpen…"
composer dump-autoload >/dev/null 2>&1
echo -n "✅"

# + Build docker-container.
echo -e "\n"
cecho "$LILA" "Bruder bauen…"
ciecho "$GREY" "Lehn dich zurück, das kann 1 bisschen dauern 😇…"
docker compose -f compose.deploy.yml build >/dev/null 2>&1

section_end

# + Start the app.
cecho "$LILA" "Endspürt…"
ciecho "$GREY" "App hochfahren…"
docker compose -f compose.deploy.yml up -d >/dev/null 2>&1

URL="http://localhost:6789"

# if command -v xdg-open >/dev/null 2>&1; then
# 	xdg-open "$URL"
# else
# 	ciecho "$GREY" "xdg-open fehlt leider, sont hätte sich das Fenster nun automatisch geöffnet 🥸"
# fi

section_end

# # DONE
if $MUSIC_DIR_LINKED; then
	cecho "$GREEN" "🤝 Fertig! Geh zu $URL - Musik wird automatisch aus deinem lokalen Musik-Ordner synchronisiert! ❤️"
else
	cecho "$GREEN" "🤝 Fertig! Geh zu $URL ❤️"
fi
