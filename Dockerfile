# Start with the official Composer image and name it 'composer' for reference
FROM composer AS composer

# get the proper 'PHP' image from the official PHP repo at
FROM php:7.2-apache-stretch

# copy the Composer PHAR from the Composer image into the apache-php image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Now that all the 'FROM' values are set, set the maintainer
MAINTAINER Ilios Project Team <support@iliosproject.org>

ENV \
COMPOSER_HOME=/tmp \
APP_ENV=prod \
ILIOS_DATABASE_URL=mysql://ilios:ilios@db/ilios \
ILIOS_DATABASE_MYSQL_VERSION=5.7 \
ILIOS_MAILER_URL=null://localhost \
ILIOS_LOCALE=en \
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt \
ILIOS_AUTHENTICATION_TYPE=form \
ILIOS_LEGACY_PASSWORD_SALT=null \
ILIOS_FILE_SYSTEM_STORAGE_PATH=/data \
ILIOS_INSTITUTION_DOMAIN=example.com \
ILIOS_SUPPORTING_LINK=null \
ILIOS_LDAP_AUTHENTICATION_HOST=null \
ILIOS_LDAP_AUTHENTICATION_PORT=null \
ILIOS_LDAP_AUTHENTICATION_BIND_TEMPLATE=null \
ILIOS_LDAP_DIRECTORY_URL=null \
ILIOS_LDAP_DIRECTORY_USER=null \
ILIOS_LDAP_DIRECTORY_PASSWORD=null \
ILIOS_LDAP_DIRECTORY_SEARCH_BASE=null \
ILIOS_LDAP_DIRECTORY_CAMPUS_ID_PROPERTY=null \
ILIOS_LDAP_DIRECTORY_USERNAME_PROPERTY=null \
ILIOS_SHIBBOLETH_AUTHENTICATION_LOGIN_PATH=null \
ILIOS_SHIBBOLETH_AUTHENTICATION_LOGOUT_PATH=null \
ILIOS_SHIBBOLETH_AUTHENTICATION_USER_ID_ATTRIBUTE=null \
ILIOS_TIMEZONE='America/Los_Angeles' \
ILIOS_REQUIRE_SECURE_CONNECTION=true \
ILIOS_KEEP_FRONTEND_UPDATED=true \
ILIOS_FRONTEND_RELEASE_VERSION=null \
ILIOS_CAS_AUTHENTICATION_SERVER=null \
ILIOS_CAS_AUTHENTICATION_VERSION=3 \
ILIOS_CAS_AUTHENTICATION_VERIFY_SSL=false \
ILIOS_CAS_AUTHENTICATION_CERTIFICATE_PATH=null \
ILIOS_ENABLE_TRACKING=false \
ILIOS_TRACKING_CODE=UA-XXXXXXXX-1

# configure Apache and the PHP extensions required for Ilios and delete the source files after install
RUN \
    apt-get update \
    && apt-get install libldap2-dev -y \
    && apt-get install zlib1g-dev \
    # remove the apt source files to save space
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && pecl channel-update pecl.php.net \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
      # enable modules
    && a2enmod rewrite socache_shmcb mpm_prefork

# copy configuration into the default locations
COPY ./docker/php.ini $PHP_INI_DIR
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# create the volume that will store the learning materials
VOLUME $ILIOS_FILE_SYSTEM_STORAGE_PATH

# copy the contents of the current directory to the /var/www/ilios directory
COPY . /var/www/ilios

# Override monolog to send errors to stdout
COPY ./docker/monolog.yaml /var/www/ilios/config/packages/prod

# add all the extra directories necessary for the application
RUN \
    mkdir -p \
    /var/www/ilios/var/cache \
    /var/www/ilios/var/log \
    # recursively change user/group ownership of the app root to 'www-data'
    && chown -R www-data:www-data /var/www/ilios \
    # give the www-data user a temporary shell in order to build the Ilios app
    && chsh -s /bin/bash www-data

# change to the context of the 'www-data' user
USER www-data

WORKDIR /var/www/ilios

# as the 'www-data' user, build the app using composer and then remove it
RUN \
    /usr/bin/composer install \
    --working-dir /var/www/ilios \
    --prefer-dist \
    --no-dev \
    --no-progress \
    --no-interaction \
    --no-suggest \
    --classmap-authoritative \
    && /usr/bin/composer clear-cache \
    && /var/www/ilios/bin/console cache:clear \
    && /var/www/ilios/bin/console cache:warmup

# switch back to the root user to finish up
USER root

# revert the 'www-data' user's shell to its default
RUN \
    chsh -s /usr/sbin/nologin www-data

# launch apache httpd as a foreground service
CMD ["apache2-foreground"]

# http is typically port 80
EXPOSE 80
