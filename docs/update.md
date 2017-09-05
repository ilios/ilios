# Keeping your Ilios up to Date

These instructions pertain to upgrades within the "Version 3"-branch of Ilios.

For upgrade instructions from Ilios 2 see [Upgrading From Ilios 2.x](upgrade_ilios_2_to_3.md).

## Frontend
The Ilios Frontend is always up-to-date on our content-delivery servers at Amazon S3, but you will need to regularly run a console command on your backend instance in order ensure that your users are seeing the latest version and that they are getting all the latest features and bugfixes. To make sure you users are receiving the latest frontend code at all times, just follow these steps:

```bash
# Log in to your Ilios API (backend) server and go to the root folder of your Ilios application ('/web/ilios3' for this example)
$ cd /web/ilios3

# Then run the 'ilios:maintenance:update-frontend' console command, in the context of your the user that runs your webservices (eg. 'apache')
$ sudo -u apache bin/console ilios:maintenance:update-frontend --env=prod
Frontend updated successfully!

# and that's it!
```
It is a good idea to run this command often (we run it in a cron job every minute).

Though you will probably never need to deploy the Ilios 3 Frontend on your own servers, the source for the frontend code can always be found at https://github.com/ilios/frontend.

## General steps

_NOTE:_ The steps below assume that file ownership of the deployed codebase belongs to the user account that
 runs your web server. Therefore, commands that affect the file system should also be performed as this user.
 In this example, this account is `apache`, but it could also be `www`, `www-data`, or something else - depending on the
 flavour of Linux (or alternate operating system) running on your server.

1. Back up your database. _Always._

2. Update your code to the latest desired version.  See https://github.com/ilios/ilios/releases for the most up-to-date list of releases.  There are zipped and tarball downloads included in the release notes for each version.  After making sure you've moved or backed-up your learning materials directory and have a copy of all the settings in you parameters.yml file, you can replace your current code entirely with the code from one of the release distributions and run the steps below.

If you use git to manage your Ilios you can update the code by checking it out using the respective tag name eg, `git checkout tags/v3.36.0`

3. Rebuild the application code via composer.

 ```bash
cd YOUR_ILIOS_APPLICATION_ROOT
sudo -u apache SYMFONY_ENV=prod composer install --no-dev --optimize-autoloader
```

4. Execute any pending database migrations.

 ```bash
cd YOUR_ILIOS_APPLICATION_ROOT
sudo -u apache bin/console doctrine:migrations:migrate --env=prod --no-interaction
```

5. Clear your application cache.

 ```bash
cd YOUR_ILIOS_APPLICATION_ROOT
sudo -u apache bin/console cache:clear --no-warmup --env=prod
sudo -u apache bin/console cache:warmup --env=prod
```

## Version-specific steps

### Upgrading to Ilios 3.36.1

A command was added in this version to fix an issue with Learning
Material metadata. After upgrading to this version you need
to run the command to scan and fix your learning materials.

```bash
cd YOUR_ILIOS_APPLICATION_ROOT
sudo -u apache bin/console ilios:maintenance:fix-mime-types --env=prod
```

### Upgrading to Ilios 3.12.0

This version adds the "AAMC Resource Type" as a new entity to the application.

Please execute the following console command _after_ running migrations 
in order to load the default resource-types data set.

```bash
cd YOUR_ILIOS_APPLICATION_ROOT
sudo -u apache bin/console doctrine:fixtures:load --env=prod --append --fixtures=src/Ilios/CoreBundle/DataFixtures/ORM/LoadAamcResourceTypeData.php
```
