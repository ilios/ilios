#!/bin/bash

APP_DIR="/srv/app"
cd $APP_DIR

# uncomment to completely rebuild opensearch index
$APP_DIR/bin/console dbal:run-sql "TRUNCATE TABLE messenger_messages;"
$APP_DIR/bin/console ilios:index:drop --force
$APP_DIR/bin/console ilios:index:create
$APP_DIR/bin/console ilios:extract-material-text --overwrite
$APP_DIR/bin/console ilios:index:update

