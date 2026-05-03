#!/bin/sh

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
