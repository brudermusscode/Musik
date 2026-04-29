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
NOCO="\033[0m"

WORK_DIR="${XDG_DATA_HOME:-$HOME/.local/share}"
BIN_DIR="${XDG_DATA_HOME:-$HOME/.local/bin}"

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

read -p "⌨️  Hast du Bock? ja/* " you_in < /dev/tty

if [ "$you_in" != "ja" ]; then
  echo -e "\n$YELLOW Alles klar Bruder, vielleicht ein andermal!$NOCO\n"
  exit 1
fi

echo -e "\n$LILA😍 Nice, dann las los gehen jetzt…$NOCO"

# Ensure the .local/share dir exists by creating it. This won't
# output anything if it already exists.
mkdir -p "$WORK_DIR"
mkdir -p "$BIN_DIR"


# + Check dependencies.
sleep 0.2
echo -e "\n$LILA🍌 Erstmal gucken, ob alles installiert ist … $NOCO"

sleep 0.2
echo -en "·"

# ! PHP installed?
command -v php >/dev/null 2>&1 || {
  echo -e "\n$YELLOW💔 Sorry Bruder, aber du musst PHP installieren. Das findest du auf https://www.php.net/manual/en/install.unix.php - Versuch es danach nochmal!$NOCO"
  exit 1
}

# ! tput (ncurses) installed?
command -v tput >/dev/null 2>&1 || {
  echo -e "\n$GREY ncurses ist nicht installiert, Terminal-Output sieht nicht so schön aus aber wird funktionieren.$NOCO"
}

sleep 0.2
echo -en "·"

# + Install composer if not exists
if command -v composer >/dev/null 2>&1; then
  sleep 0.2
  echo -e "\n$YELLOW♻️  Installiere composer in »~/.local/bin«$NOCO"

  curl -sS https://getcomposer.org/installer | php
  echo -e "Moving composer to »~/.local/bin«"
  mv composer.phar $BIN_DIR/composer
  echo -e "Good went!"
fi

exit 1;

sleep 0.2
echo -en "·"

sleep 0.2
echo -e "\n✅ Alles cool!"



# Set working dir.
cd $WORK_DIR

# Only for testing locally.
# cd musikbruder && docker compose -f compose.deploy.yml down --volume && cd .. && rm -rf musikbruder

echo -en "\n🍎"
sleep 0.2
echo -n "🍊"
sleep 0.2
echo -n "🍉"
sleep 0.2
echo -n "🍇"
sleep 0.2
echo -n "🍋"
sleep 0.2
echo -n "🍑"
sleep 0.2
echo -n "🥭"
sleep 0.2
echo -n "🥐"
sleep 0.2
echo -n "🧀"
sleep 0.2
echo -n "🍓"
sleep 0.2
echo -n "."
sleep 0.2
echo -n "."
sleep 0.2
echo -n "."
sleep 0.2
echo -en "🎌\n"
sleep 0.2



# + Clone GitHub
sleep 0.2
echo -e "\n$LILA♻️  App von GitHub klonieren…$NOCO"

# Clone the git repository here and cd into it, so we set the new
# working directory.
git clone https://github.com/brudermusscode/UnSpotify.git musikbruder
cd musikbruder


# + Dependency directories and files.
sleep 0.2
echo -e "\n$LILA♻️  Alle Relevanzen erstellen…$NOCO"

# Create assets file structure.
mkdir -p public/data/user/1/{tracks,art,portraits,videos}
mkdir -p public/data/user/1/tracks/deleted
mkdir -p storage/logs
chmod a+rw -R storage public sql

touch sql/last_migration
chmod a+rw sql/last_migration

cp .env.example .env

echo -e "✅ Alles cool!"


# + Composer
sleep 0.2
echo -e "\n$LILA♻️  Nun alle composer Relevanzen…$NOCO"


# Install composer & dump autoload.
if command -v tput >/dev/null 2>&1; then
  # reserve 5 empty lines for the cursor to move up and delete. This
  # makes the live window log possible without deleting 5 lines of
  # output from commands before.
  printf '\n%.0s' {1..5}
  composer install 2>&1 | while IFS= read -r line; do
    buffer+=("$line")
    if [ "${#buffer[@]}" -gt 5 ]; then
        buffer=("${buffer[@]:1}")
    fi

    # move cursor up 5 lines without clearing history
    tput sc
    tput cuu 5 2>/dev/null

    printf "$GREY\033[2K%s\n" "${buffer[@]}"

    tput rc
  done
else
  composer install
fi
composer dump-autoload


# + Build docker-container.
sleep 0.2
echo -e "\n$LILA♻️  Bruder bauen…$NOCO"

docker compose -f compose.deploy.yml build --no-cache


# + Start the app.
sleep 0.2
echo -e "\n$LILA♻️  App starten…$NOCO"
docker compose -f compose.deploy.yml up -d

URL="http://localhost:6789"

# # Done!
sleep 0.2
if command -v xdg-open >/dev/null 2>&1; then
  xdg-open "$URL"
else
  echo -e "\nxdg-open fehlt leider, sont hätte sich das Fenster nun automatisch geöffnet 🥸"
fi

echo -e "\n$GREEN🤝 Fertig! Geh zu $URL - Deine Musik kannst du in ~/.local/share/musikbruder/public/data/user/1/tracks packen ❤️"
