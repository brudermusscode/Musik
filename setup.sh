#!/bin/sh

# [ -d "$HOME/.local/share" ] || {
#   echo "~/.local/share gibt's nicht!"
#   exit 1
# }

# Check if test is requested.
for arg in "$@"; do
	case "$arg" in
	--test) TESTING=true ;;
	--no-cache) CACHELESS=true ;;
	esac
done

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

HOME_DIR=${XDG_DATA_HOME:-$HOME}
WORK_DIR="$HOME_DIR/.local/share"
BIN_DIR="$HOME_DIR/.local/bin"
LOG_DIR="$HOME_DIR/.logs/musikbruder"
LOG_FILE=$LOG_DIR/setup.log

# Ensure the .local/share dir exists by creating it. This won't
# output anything if it already exists.
mkdir -p "$WORK_DIR"
mkdir -p "$BIN_DIR"

# Create loging directory and a file for this setup.
mkdir -p "$LOG_DIR"

if [ -f "$LOG_FILE" ]; then
	rm -f $LOG_DIR/setup.log
fi

touch $LOG_FILE

echo -e ""

# + Check dependencies.
# ! PHP installed?
# command -v php >/dev/null 2>&1 || {
# 	echo -e "\n$YELLOW💔 Sorry Bruder, aber du musst PHP installieren. Das findest du auf https://www.php.net/manual/en/install.unix.php - Versuch es danach nochmal!$NOCO"
# 	exit 1
# }

# + Install composer
# cecho "$LILA" "Dependencies!"

# if ! command -v composer >/dev/null 2>&1; then
# 	ciecho "$GREY" "Installiere composer…"
# 	curl -sS https://getcomposer.org/installer | php >>"$LOG_FILE" 2>&1
# 	ciecho "$GREY" "In »~/.local/bin« verschieben…"
# 	mv composer.phar $BIN_DIR/composer
# fi

# section_end

# Set working dir..
cd $WORK_DIR

[ -n "$TESTING" ] && cecho "$RED" "Test-Modus aktiviert"
[ -n "$CACHELESS" ] && cecho "$RED" "Cache wird umgangen"

echo -e ""

sleep 0.2
# + Download App
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
		docker compose -f compose.deploy.yml down --volumes >>"$LOG_FILE" 2>&1
		cd ..
		rm -rf musikbruder
	fi
fi

# + Clone GitHub
ciecho "$GREY" "Kloniere GitHub Repo…"
{
	[ -z "$TESTING" ] &&
		git clone https://github.com/brudermusscode/MusikBruder.git musikbruder ||
		git clone --branch test --single-branch https://github.com/brudermusscode/MusikBruder.git musikbruder
	cd musikbruder
} >>"$LOG_FILE" 2>&1

section_end

# + Dependency directories and files.
# {
cecho "$LILA" "Alle Relevanzen erstellen…"
ciecho "$GREY" "Mach ich Bruder, warte kurz…"
ciecho "$GREY" "Ordner erstellen…"
mkdir -p public/data/user/1/{tracks,art,portraits,videos}
mkdir -p public/data/user/1/tracks/deleted
mkdir -p storage/logs
touch storage/logs/setup.log
touch sql/last_migration
ciecho "$GREY" "Rechte einstellen…"
chmod a+rw -R storage public sql
chmod a+rw sql/last_migration
ciecho "$GREY" "Environment erstellen…"
cp .env.example .env
# } >>"$LOG_FILE" 2>&1

# + Link Music Directory
MUSIC_DIR="$HOME_DIR/Music"
MUSIC_DIR_LINKED=false
REPLACEMENT=""

if [ -d $MUSIC_DIR ]; then
	read -p "$(echo -e "${GREY}╟${NOCO}  Willst du deinen lokalen Musik-Ordner synchronisieren? [ja]/[nein] ⌨️  ")" link_music </dev/tty

	if [ "$link_music" == "ja" ]; then
		MUSIC_DIR_LINKED=true
		REPLACEMENT="- $MUSIC_DIR:/data/public/data/user/1/tracks/local"
	fi
fi

{
	# If local music shall be linked, create a /local folder inside
	# the sync base dir in the app.
	if $MUSIC_DIR_LINKED; then
		mkdir -p public/data/user/1/tracks/local
	fi

	# Replace variable placeholders with the actual replacement and
	# create a real compose file for upping docker.
	sed -i "s|%MUSIC_DIR_AS_VOLUME%|${REPLACEMENT}|g" compose-dummy
	mv compose-dummy compose.deploy.yml
} >>"$LOG_FILE" 2>&1

section_end

# + Composer
# TODO: Build composer inside Dockerfile. Removes php dependency!
# cecho "$LILA" "Composer?"
# ciecho "$GREY" "Ist wichtig, versprochen…"
# composer install >>"$LOG_FILE" 2>&1
# ciecho "$GREY" "Alle Relevanzen dumpen…"
# composer dump-autoload >>"$LOG_FILE" 2>&1
# echo -n "✅"

# + Build docker-container.
cecho "$LILA" "Bruder bauen…"
ciecho "$GREY" "Lehn dich zurück, das kann 1 bisschen dauern 😇…"
docker compose -f compose.deploy.yml build ${CACHELESS:+--no-cache} >>"$LOG_FILE" 2>&1

section_end

# + Start the app.
cecho "$LILA" "Endspürt…"
ciecho "$GREY" "App hochfahren…"
docker compose -f compose.deploy.yml up -d >>"$LOG_FILE" 2>&1

URL="http://localhost:6789"

# if command -v xdg-open >>$LOG_FILE 2>&1; then
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
