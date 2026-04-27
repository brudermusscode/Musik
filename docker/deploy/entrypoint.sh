#!/bin/sh

# exit on any failure
set -e
# exit on unassigned variable
set -u

command=false
if [ "$#" -gt 0 ]; then
    command="$1"
    shift
fi

case "$command" in
    nginx) exec nginx -c /data/docker/deploy/nginx.conf "$@";;
    *) exec "$command" "$@";;
esac