#!/bin/bash

APP_DIR="/srv/app"
cd $APP_DIR

# consume the async messages
$APP_DIR/bin/console messenger:consume async_priority_high async_priority_normal async_priority_low "$@"

