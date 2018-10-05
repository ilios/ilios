# Using Environmental Variables for Ilios Configuration Settings

If you are familiar with versions of Ilios prior to v3.56.0, you have probably already noticed that there is no longer a `parameters.yml` file for managing the configuration settings of an Ilios instance, including its database connection settings and credentials.  As of Ilios v3.56.0, the parameters.yml file has been removed in favor of setting the configuration variables as 'Runtime Environment Variables' within the context of the user that runs your web service processes on the system (typically 'apache', 'www', 'nginx', etc).

Prior to October 2017, most of the Ilios configuration options could be found in a 'parameters.yml' YAML file located in the `config/` folder of the Ilios application but, over the past year, we have deprecated this method in favor of storing these values in the database or, where that is not possible (eg, database settings and credentials), setting them as user runtime environment variables instead.

Currently, almost all of the configuration settings for Ilios are stored within the `application_config` table of the Ilios database, except as noted below. The values listed here must be set in the local user's environment for initial installation and when running console commands on the command line and, for the actual web-server processes, these variables must also be set in the context of the user/daemon that runs the web services while the services are running (eg, `apache`, etc).

Before attempting to install or update an Ilios instance, please make sure the following values are set and present in your user's system environment as well as the system environment of the user that runs the web services on your system:

```bash
# the application environment variable 'APP_ENV' should always be set, and it will almost always be set to `prod` 
APP_ENV=prod

# the authentication should be set to whichever type of user authentication you use (`form`,`cas`,`shibboleth`,`ldap`)
ILIOS_AUTHENTICATION_TYPE=form

# the database connection settings, in url format (eg, mysql://[username]:[password]@[database-hostname]/[database_name]
ILIOS_DATABASE_URL="mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db"

# the version of the MySQL database you are using
ILIOS_DATABASE_MYSQL_VERSION=5.7

# the locale of the Ilios instance (which language to use: `en`,`fr`,`es`)
ILIOS_LOCALE=en

# the hash secret for encrypting strings within the Ilios Application
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt

# the location of the learning materials on your system
ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/ilios/learning_materials

# the mailer url
ILIOS_MAILER_URL=null
```

To see which environment variables are set for your respective user, you can run the `env` command like so:
```bash
env
```
When you run this command, all of your current environment variables will be displayed and will look something like the following:

```bash
HOSTNAME=ilios-web.example.com
TERM=xterm-color
SHELL=/bin/bash
PWD=/var/www/ilios
LANG=en_US.UTF-8
PS1=[\e[0;33m\]\u\[\e[0m\]@\[\e[31m\]\h\[\e[39m\]:\w]\n$
CLICOLOR=1
LSCOLORS=ExFxCxDxBxegedabagacad
HISTSIZE=1000
MAIL=/var/spool/mail/ilios_user
PATH=/usr/local/bin:/usr/bin:/usr/local/sbin:/usr/sbin:/home/ilios_user/.local/bin:/home/ilios_user/bin
HOME=/home/ilios_user
ILIOS_MAILER_URL=null
ILIOS_AUTHENTICATION_TYPE=form
ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/files/learning_materials
ILIOS_DATABASE_URL=mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db
ILIOS_DATABASE_MYSQL_VERSION=5.7
ILIOS_LOCALE=en
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt
APP_ENV=prod
```

You'll want to verify that the `APP_ENV` variable is set (typically to `prod`) and that the other variables shown above, and prefixed by `ILIOS_` are also present.

# Setting ENV vars

## Setting Environment Variables for each Command-Line User

Setting the ENV variables for a given user on a system is typically a pretty straight-forward process and, for installation and maintenance of Ilios using console commands, you would just need to set the required ENV variables directly on the command-line, in a script at runtime, or have them already set upon login by declaring them in one of the respective user's initialization scripts (eg, .bashrc, .bash_profile`, `.profile`, etc.).

You can set these runtime variables in one of 3 ways:

#### Example #1 - Setting ENV vars upon login by exporting them using a user-initialization script
```bash
# Export these vars directly from within your preferred user initialization script (.bash_profile, .bashrc, .profile) and 
# they will be set and ready to be used upon login to the terminal and throughout the entirety of your user session

export APP_ENV=prod
export ILIOS_MAILER_URL=null
export ILIOS_AUTHENTICATION_TYPE=form
export ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/files/learning_materials
export ILIOS_DATABASE_URL=mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db
export ILIOS_DATABASE_MYSQL_VERSION=5.7
export ILIOS_LOCALE=en
export ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt
```
Doing it this way ensures that these ENV vars will always be available to your user while you work and/or run commands directly from the command line.

#### Example #2 - Setting ENV vars at the top of a shell script that runs the desired console command

Let's say that you are going to run the `/bin/setup` command to install a completely new version of Ilios.  For this approach, you would create a shell script that runs `bin/setup` as its final step, and set the ENV vars above it in the file, like so:
```bash
#!/bin/bash

# set the environment variables
ILIOS_APP_DIR=/var/www/ilios
APP_ENV=prod
ILIOS_MAILER_URL=null
ILIOS_AUTHENTICATION_TYPE=form
ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/files/learning_materials
ILIOS_DATABASE_URL=mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db
ILIOS_DATABASE_MYSQL_VERSION=5.7
ILIOS_LOCALE=en
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt

#run the setup command
$ILIOS_APP_DIR/bin/setup
```

Note that doing it this way will only set the ENV variables for the duration of the execution of the `bin/setup` command and you will need to create/update a script like this for each command-line command you want to run.

#### Example #3 - Setting ENV vars at the command line directly

Technically, you can set all of the ENV vars at the command line on the same line and at the same time that you run the `bin/setup` command, like so:

```bash
APP_ENV=prod ILIOS_MAILER_URL=null ILIOS_AUTHENTICATION_TYPE=form ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/files/learning_materials ILIOS_DATABASE_URL=mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db ILIOS_DATABASE_MYSQL_VERSION=5.7 ILIOS_LOCALE=en ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt bin/setup
```
Doing it this way is not recommended, however, as it would require you to set the ENV vars ANY and EVERY time you ran any command from the command line.  As in example 2, the ENV vars set in this way only persist for the duration of the execution of the command being run.
  
## Setting Environment Variables for the Web-Services user

The user running the web server processes on your system will most likely NOT have an environment with a typical login shell like `BASH` or `csh`, so setting the ENV vars cannot be done with the usual method (eg, prefixing the command with `APP_ENV=prod ILIOS_SECRET='SomethingSecret'` vars at the command line). In these cases, the web service user's ENV variable values need to be set in the web server configuration directly (eg, within the `httpd.conf` file on Apache) or within the web service user's initialization script (eg, `/etc/sysconfig/httpd` or `/etc/apache/envvars`, depending on your flavor of Linux).

##### (NOTE: At this time, we only have instructions for configuring ENV variables on an Apache 2.4.x web server. Documentation for Nginx servers is forthcoming...)

#### Setting Web Service ENV vars via Web Service User Initialization Scripts (Apache httpd)

By populating one of these web service user-specific initialization scripts with the ENV vars in the same way as shown for a shell script (see Example #2 above), the variables will be accessible by the web daemon/service while the service remains running.  For example, on a RHEL system (RedHat, CentOS, or Fedora), we would populate the `/etc/sysconfig/http` file with following content:

```
APP_ENV=prod
ILIOS_MAILER_URL=null
ILIOS_AUTHENTICATION_TYPE=form
ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/files/learning_materials
ILIOS_DATABASE_URL=mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db
ILIOS_DATABASE_MYSQL_VERSION=5.7
ILIOS_LOCALE=en
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt
``` 

#### Setting Web Service ENV vars via the Web Service Configuration Files (Apache httpd)

In order to set runtime environment variables within the Apache httpd web service configuration itself, you will need to enable the `mod_env` Apache module and set the `SetEnv` directives in the appropriate section(s) of your httpd configuration files as shown:

```
SetEnv ILIOS_DATABASE_URL mysql://ilios_db_user:Pa$$w0rd@db-host1/ilios_db
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

SetEnvIf Host "ilios-staging\.example\.com" ILIOS_DATABASE_URL=SetEnv ILIOS_DATABASE_URL mysql://ilios_staging_db_user:Stag1ngPassw0rd@db-host1/ilios_stage_db
SetEnvIf Host "ilios-staging\.example\.com" ILIOS_DATABASE_USER=ilios_staging_db_user
SetEnvIf Host "ilios-staging\.example\.com ILIOS_SECRET=ST@G1nGS3CRET12345

SetEnvIf Host "ilios-production\.example\.com" ILIOS_DATABASE_URL=mysql://ilios_production_db_user:Pr0duct10nPassw0rd@/db-host1/ilios_production_db
SetEnvIf Host "ilios-production\.example\.com ILIOS_SECRET=PR0DUCT10nS3CRET12345
```

For more information on Apache Environments, `SetEnv`, and `SetEnvIf`, please refer to the Apache 2.4.x documentation at https://httpd.apache.org/docs/2.4/env.html

## Verifying your Environment Variables

#### Verifying the Environment at the Command Line

If you would like to verify the env variables for yourself or a different user on command line, run the following commands:

```bash
# for yourself
env
```

```bash
# to check command line ENV vars for a different user:
sudo -u [USER_NAME] env
```

#### Verifying the Environment on the Web Server
To verify which ENV vars, if any, are set for within your web service, create a php file on your webserver with the following content and then view the file in your browser:

```php
<?php
phpinfo();
```

For Apache-based web servers, the currently-set Environment Variables should appear under the 'Apache Environment' or 'Environment' sections of the output.  If they are not set, you need to set them via one of the appropriate configurations as described above.

#### Setting ENV vars globally and concurrently (command-line AND web-services users)

Regardless of how you plan to use Ilios, we know that you will be working with it heavily at both the command line, for scheduled 'cron' jobs and maintenance tasks, AND as a web-service, for hosting the application GUI.  We also know that maintaining multiple environments for each use-case (command line users vs. web-service) can be a hassle, especially when the ENV values would typically be the same between the two and could effectively be shared.  

Unfortunately, there is no easy "standard" solution for sharing the ENV variables between command-line use-cases and the web service, so we've come up with a bit of a workaround for accomplishing this and we thought we'd mention it here. While this isn't necessarily a recommendation or a "best practice" per sé, we have had some success in keeping all of our required ENV vars in-sync between our command-line `apache` user and the `apache` user running our web service by using the following method:

On RHEL systems (Redhat, CentOS, and Fedora), an init file exists at `/etc/environment` for populating environment variables for every command-line user. Upon login, it adds its environment settings in addition to those set by the users' individual init scripts. To keep the ENV var settings in-sync between the apache web-service and any logged-in command-line user, we do the following:

We populate the `apache` web service user's init script at `/etc/sysconfig/httpd` with following content:

```bash
APP_ENV=prod
ILIOS_MAILER_URL=null
ILIOS_AUTHENTICATION_TYPE=form
ILIOS_FILE_SYSTEM_STORAGE_PATH=/var/www/files/learning_materials
ILIOS_DATABASE_URL=mysql://ilios_user:ili0s_passw0rd@database.example.com/ilios_db
ILIOS_DATABASE_MYSQL_VERSION=5.7
ILIOS_LOCALE=en
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt
```

And then we delete the global `/etc/environment` init script, and recreate it as a symbolic link pointing to the `/etc/sysconfig/httpd` web service init script with the following command(s):

```bash
sudo rm /etc/environment && sudo ln -s /etc/sysconfig/httpd /etc/environment
```

The idea behind this is that EVERYONE who logs in to the system will have the properly-configured ENV settings that match those set for the apache httpd web service user by having the two init scripts point to the exact same file.  One file ensures the settings will ALWAYS be in-sync. If you'd like to try this, but are confused, or if you can come up with a better way for managing this file-synchronicity, please do not hesitate to contact us at support@iliosproject.org with any questions or suggestions!

## Why Environment Variables?

Many users have asked why we have decided to deprecate/remove the `parameters.yml` file for ENV vars and there are several reasons that are best summed up by the makers of the Symfony framework in their [Symfony 3.2 release notes](https://symfony.com/blog/new-in-symfony-3-2-runtime-environment-variables), from when they first introduced the change themselves:

>[Configuration Options set as Runtime ENV variables] are one of the main concepts of the [twelve-factor app methodology](https://12factor.net/). Their main advantages are that they can be changed between deploys without changing any code and that they don't need to be checked into the code repository.
