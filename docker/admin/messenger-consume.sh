#!/bin/bash

APP_DIR="/srv/app"
cd $APP_DIR

# check for LOCK_DSN env var, and if empty, set it to 'flock'
export LOCK_DSN=${LOCK_DSN:-"flock"}

# consume the async messages
bin/console messenger:consume async

