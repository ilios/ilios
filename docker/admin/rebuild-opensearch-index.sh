#!/bin/bash

APP_DIR="/srv/app"
cd $APP_DIR

# check for LOCK_DSN env var, and if empty, set it to 'flock'
export LOCK_DSN=${LOCK_DSN:-"flock"}

bin/console dbal:run-sql "TRUNCATE TABLE messenger_messages;"
bin/console ilios:index:drop --force
bin/console ilios:index:create
bin/console ilios:extract-material-text --overwrite
bin/console ilios:index:update

