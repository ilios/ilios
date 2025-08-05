#!/bin/bash

APP_DIR="/srv/app"

#now rebuild the app
cd $APP_DIR

# force remove all already-built dependencies just in case
rm -Rf vendor
rm -Rf var/cache/*

# rebuild/install via composer
/usr/bin/composer install --no-dev --prefer-dist
echo "composer install has run"
/usr/bin/composer dump-autoload  --optimize
echo "composer dump-autoload has run"

$APP_DIR/bin/console cache:clear --no-warmup
$APP_DIR/bin/console cache:warmup
echo "Cache warmed up"

# migrate the db
$APP_DIR/bin/console doctrine:migrations:migrate -n
echo "migrations run!"

# sync the metadata storage
$APP_DIR/bin/console doctrine:migrations:sync-metadata-storage
echo "metadata storage synced!"

# update the frontend if container is web-facing
#$APP_DIR/bin/console ilios:maintenance:update-frontend

# uncomment to completely rebuild opensearch index
#$APP_DIR/bin/console dbal:run-sql "TRUNCATE TABLE messenger_messages;"
#$APP_DIR/bin/console ilios:index:drop --force
#$APP_DIR/bin/console ilios:index:create
#$APP_DIR/bin/console ilios:extract-material-text --overwrite
#$APP_DIR/bin/console ilios:index:update

