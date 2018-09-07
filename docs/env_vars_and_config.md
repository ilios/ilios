# Using Environmental Variables for Ilios Configuration Settings

When Ilios v3.56.0 is released in October 2018, there will no longer be a [parameters.yml](https://github.com/ilios/ilios/blob/master/app/config/parameters.yml.dist) file for setting some of the Ilios configuration variables, including database connection settings and credentials.  At that time, you will need to provide these settings as 'Runtime Environment Variables' set in the context of the user that runs your web service processes on the system (typically 'apache', 'www', 'nginx', etc).

Prior to October 2017, most of the Ilios configuration options were set in the `parameters.yml` yaml file located at `[ILIOS APPLICATION DIRECTORY]/app/config/parameters.yml` but, over the past year or so, we've been slowly deprecating this method in favor of storing them in the database or, where that is not possible (eg, database settings and credentials), setting them as user runtime environment variables instead.

By now (September 2018), almost all of the configuration settings are stored in the `application_config` table of the Ilios database, except as noted below.  The [parameters.yml](https://github.com/ilios/ilios/blob/master/app/config/parameters.yml.dist) file can still be used for now, but it currently exists to be used as a 'hybrid' solution that sets the configuration variables as runtime environment variables using the following syntax within the file:

```yaml
# This file is auto-generated during the composer install
parameters:
    env(ILIOS_DATABASE_HOST): db-host.example.com
    env(ILIOS_DATABASE_PORT): 3306
    env(ILIOS_DATABASE_NAME): ilios_db
    env(ILIOS_DATABASE_USER): ilios_db_user
    env(ILIOS_DATABASE_PASSWORD): XXXXXXXXX
    env(ILIOS_DATABASE_MYSQL_VERSION): 5.7
    env(ILIOS_MAILER_TRANSPORT): smtp
    env(ILIOS_MAILER_HOST): 127.0.0.1
    env(ILIOS_MAILER_USER): null
    env(ILIOS_MAILER_PASSWORD): null
    env(ILIOS_LOCALE): en
    env(ILIOS_SECRET): ThisTokenIsNotSoSecretChangeIt
```

When Ilios v3.56.0 is released, the [parameters.yml](https://github.com/ilios/ilios/blob/master/app/config/parameters.yml.dist) will be removed from the codebase entirely, so you will need to ensure that each of the necessary config values are properly set in the web service user's respective system environment.

## Why Environment Variables?

Many users have asked why we have decided to deprecate/remove the `parameters.yml` file for ENV vars and there are several reasons that are best summed up by the makers of the Symfony framework in their [Symfony 3.2 release notes](https://symfony.com/blog/new-in-symfony-3-2-runtime-environment-variables), from when they first introduced the change themselves:

>[Configuration Options set as Runtime ENV variables] are one of the main concepts of the [twelve-factor app methodology](https://12factor.net/). Their main advantages are that they can be changed between deploys without changing any code and that they don't need to be checked into the code repository.

## Configuring Environment Varialbles

Whether you are installing a fresh copy of Ilios or updating a previous version, the first step you should take before installing OR updating to Ilios 3.56.0 is to make sure that the following variables are present and set in the Runtime Environment of your your web service user:

```
ILIOS_DATABASE_HOST
ILIOS_DATABASE_PORT
ILIOS_DATABASE_NAME
ILIOS_DATABASE_USER
ILIOS_DATABASE_PASSWORD
ILIOS_DATABASE_MYSQL_VERSION
ILIOS_MAILER_TRANSPORT
ILIOS_MAILER_HOST
ILIOS_MAILER_USER
ILIOS_MAILER_PASSWORD
ILIOS_LOCALE
ILIOS_SECRET
```

The user running the web server processes on your system will most likely NOT have an environment with a typical login shell like `BASH` or `csh`, so setting the ENV vars cannot be done with the usual method (eg, running `export ILIOS_DATABASE_HOST='foo.example.com'` at the command line). In these cases, the web service user's ENV variable values need to be set in the web server configuration directly (eg, within the `httpd.conf` file on Apache).

To verify which of these ENV vars, if any, are set for your web service user, create a php file on your webserver with the following content and then view the file in your browser:

```php
<?php
phpinfo();

```

For Apache-based web servers, the currently-set Environment Variables should appear under the 'Apache Environment' section of the output.  If they are not set, you need to set them in the appropriate configuration files as described below.

**(NOTE: At this time, we only have instructions for configuring ENV variables on an Apache 2.4.x server. Documentation for Nginx servers is forthcoming...)**

### Setting ENV vars on an Apache Httpd server

#### Single Ilios instance on single Apache httpd server:
In order to set runtime environment variables for the user that runs the Apache Httpd web service (eg, 'apache', 'www', 'httpd', etc...), you will need to enable the `mod_env` Apache module and set the `SetEnv` directives in the appropriate section(s) of your httpd configuration files as shown:

```
SetEnv ILIOS_DATABASE_HOST db-host1.example.com
SetEnv ILIOS_DATABASE_PASSWORD P@$$w0rd
SetEnv ILIOS_DATABASE_NAME ilios_db
SetEnv ILIOS_DATABASE_USER ilios_db_user
SetEnv ILIOS_DATABASE_MYSQL_VERSION 5.7
SetEnv ILIOS_DATABASE_PORT 3306
SetEnv ILIOS_MAILER_TRANSPORT smtp
SetEnv ILIOS_MAILER_HOST 127.0.0.1
SetEnv ILIOS_MAILER_USER null
SetEnv ILIOS_MAILER_PASSWORD null
SetEnv ILIOS_LOCALE en
SetEnv ILIOS_SECRET ThisTokenIsNotSoSecretChangeIt
```

#### Multiple Ilios instances on single Apache httpd server:
If you are running more than Ilios instance (eg, production and staging instances) on a single Apache httpd server using multiple VirtualHost configurations, certain variables will collide (eg, `ILIOS_DATABASE_NAME`), so these will need to be set conditionally.  In order to do this, you will need to install/enable the `mod_setenvif` Apache module and set the 'SetEnvIf' directives in the appropriate section(s) of your httpd configuration files as shown.  Note that the values shared between the two instances are set using `SetEnv`, while the conditional values are set with `SetEnvIf` and require a specific condition to be true in order to be set.  

```
SetEnv ILIOS_DATABASE_MYSQL_VERSION 5.7
SetEnv ILIOS_MAILER_TRANSPORT smtp
SetEnv ILIOS_MAILER_HOST 127.0.0.1
SetEnv ILIOS_MAILER_USER null
SetEnv ILIOS_MAILER_PASSWORD null
SetEnv ILIOS_LOCALE en
SetEnv ILIOS_DATABASE_HOST db-host.example.com
SetEnv ILIOS_DATABASE_PORT 3306

SetEnvIf Host "ilios-staging\.example\.com" ILIOS_DATABASE_NAME=ilios_staging_db
SetEnvIf Host "ilios-staging\.example\.com" ILIOS_DATABASE_USER=ilios_staging_db_user
SetEnvIf Host "ilios-staging\.example\.com" ILIOS_DATABASE_PASSWORD=St@g1ngP@ssw0rd
SetEnvIf Host "ilios-staging\.example\.com ILIOS_SECRET=ST@G1nGS3CRET12345

SetEnvIf Host "ilios-production\.example\.com" ILIOS_DATABASE_NAME=ilios_production_db
SetEnvIf Host "ilios-production\.example\.com" ILIOS_DATABASE_USER=ilios_production_db_user
SetEnvIf Host "ilios-production\.example\.com" ILIOS_DATABASE_PASSWORD=Pr0duct10nP@ssw0rd
SetEnvIf Host "ilios-production\.example\.com ILIOS_SECRET=PR0DUCT10nS3CRET12345
```

For more information on Apache Environments, `SetEnv`, and `SetEnvIf`, please refer to the Apache 2.4.x documentation at https://httpd.apache.org/docs/2.4/env.html
