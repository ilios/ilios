#!/bin/sh

# ilios/console-command
# runs a single Ilios console command and exits
# 
# usage: docker run -e ENTRYPOINT="bin/console [COMMAND]" yourimage

if [ -z "$ENTRYPOINT" ]; then
  echo "Please supply entrypoint argument as ENTRYPOINT env var."
  exit 1
else
  cd /srv/app || exit 2
  exec sh -c "$ENTRYPOINT"
fi

