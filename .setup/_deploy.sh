__DIR__="$(cd "$(dirname "$0")" && pwd)"
test_branch="test"
deploy_branch="deploy"

source $__DIR__/functions.sh

# + deploy setup script
echo -e ""
cecho "$LILA" "Deployment gestartet!"
ciecho "$GREY" "Setup-Script auf heia.kim pushen…"

# Ask to test the new setup first. It will upload the setup.sh to setup-test.sh
read -p "$(echo -e "${GREY}╟${NOCO}  Testen? [ja]/[nein] ⌨️  ")" test
[ $test == "ja" ] && suffix="-test" || suffix=""
rsync --progress -a "$__DIR__/setup.sh" \
  Hetzner:/var/opt/heiakim/Devlog/public/MusikBruder/setup$suffix.sh

# Ask for commiting and pushing changes to github.
read -p "$(echo -e "${GREY}╟${NOCO}  Auch den Rest? [ja]/[nein] ⌨️  ")" all
if [ "$all" != "ja" ]; then
  exit 1
fi

# + build production assets
ciecho "$GREY" "Production assets bauen…"
pnpm run prod

# + git commit + push
ciecho "$GREY" "Git commiten & pushen…"
if ! git diff --quiet; then
  git add .

  # Set custom commit message
  read -p "$(echo -e "${GREY}╟${NOCO}  Commit-Beschreibung ⌨️  ")" commit_message
  if [ "$commit_message" == "" ]; then
    commit_message="[🥦] deploy.sh pushed this!"
  fi

  git commit -m "$commit_message"
  [ $test == "ja" ] && git push origin $test_branch || git push origin $deploy_branch
else
  ciecho "$GREY" "Git ist sauber…"
fi

ciecho "$GREY" "Alles cool!"

section_end
