#!/bin/bash

APP_DIR="/srv/app"

#now rebuild the app
cd $APP_DIR

# force remove all already-built dependencies just in case
rm -Rf vendor
rm -Rf var/cache/*

# rebuild/install via composer
composer install --no-dev --prefer-dist
echo "composer install has run"
composer dump-autoload  --optimize
echo "composer dump-autoload has run"

bin/console cache:clear --no-warmup
bin/console cache:warmup
echo "Cache warmed up"

# migrate the db
bin/console doctrine:migrations:migrate -n
echo "migrations run!"

# sync the metadata storage
bin/console doctrine:migrations:sync-metadata-storage
echo "metadata storage synced!"

# update the frontend
#bin/console ilios:maintenance:update-frontend

# uncomment to completely rebuild opensearch index
#sudo -u www-data bin/console doctrine:query:sql "TRUNCATE TABLE messenger_messages;"
#bin/console dbal:run-sql "TRUNCATE TABLE messenger_messages;"
#bin/console ilios:index:drop --force
#bin/console ilios:index:create
#bin/console ilios:extract-material-text --overwrite
#bin/console ilios:index:update

