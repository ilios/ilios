###############################################################################
# Contains all of the ilios src code for use in other containers
###############################################################################
FROM scratch as src
COPY composer.* symfony.lock LICENSE /src/
COPY config /src/config/
COPY custom /src/custom/
COPY src /src/src/
COPY templates /src/templates/
COPY migrations /src/migrations/
COPY bin/console /src/bin/
COPY public/index.php /src/public/
COPY public/theme-overrides/ /src/public/theme-overrides/

# Override monolog to send errors to stdout
COPY ./docker/monolog.yaml /src/config/packages/prod

###############################################################################
# Nginx Configured to Run Ilios from an FPM host
###############################################################################
FROM nginx:1.19-alpine as nginx
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY --from=src /src /var/www/ilios
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
ENV FPM_CONTAINERS=fpm:9000
ARG ILIOS_VERSION="v0.1.0"
RUN echo ${ILIOS_VERSION} > VERSION

###############################################################################
# Dependencies we need in all PHP containers
# Production ready composer pacakges installed
###############################################################################
FROM php:8.0-fpm as php-base
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=src /src /var/www/ilios

# configure Apache and the PHP extensions required for Ilios and delete the source files after install
RUN \
    apt-get update \
    && apt-get install libldap2-dev libldap-common zlib1g-dev libicu-dev libzip-dev libzip4 unzip -y \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install intl \
    && mkdir -p /usr/src/php/ext/apcu \
    && curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 \
    && docker-php-ext-install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable opcache \
    && rm -rf /var/lib/apt/lists/* \
    # remove the apt source files to save space
    && apt-get purge libldap2-dev zlib1g-dev libicu-dev -y \
    && apt-get autoremove -y

ENV \
COMPOSER_HOME=/tmp \
APP_ENV=prod \
APP_DEBUG=false \
MAILER_DSN=null://null \
ILIOS_LOCALE=en \
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt \
ILIOS_REQUIRE_SECURE_CONNECTION=false \
MESSENGER_TRANSPORT_DSN=doctrine://default

WORKDIR /var/www/ilios
RUN /usr/bin/touch .env
RUN /usr/bin/composer install \
    --prefer-dist \
    --no-dev \
    --no-progress \
    --no-interaction \
    --no-suggest \
    --classmap-authoritative \
    #creates an empty env.php file, real ENV values will control the app
    && /usr/bin/composer dump-env prod \
    && /usr/bin/composer clear-cache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./docker/php.ini $PHP_INI_DIR/conf.d/ilios.ini
#Override the default entrypoint script with our own
COPY docker/php-fpm-entrypoint /usr/local/bin/docker-php-entrypoint
ARG ILIOS_VERSION="v0.1.0"
RUN echo ${ILIOS_VERSION} > VERSION

###############################################################################
# FPM configured to run ilios
# Really just a wrapper around php-base, but here in case we need to modify it
###############################################################################
FROM php-base as fpm
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"

###############################################################################
# FPM configured for development
# Runs a dev environment and composer dependencies
###############################################################################
FROM fpm as fpm-dev
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
ENV APP_ENV dev
ENV APP_DEBUG true
# Remove opcache production only optimizations
RUN sed -i '/^opcache\.preload/d' $PHP_INI_DIR/conf.d/ilios.ini
RUN sed -i '/^opcache\.validate_timestamps/d' $PHP_INI_DIR/conf.d/ilios.ini

RUN /usr/bin/composer install \
  --working-dir /var/www/ilios \
  --no-progress \
  --no-suggest \
  --no-interaction

###############################################################################
# Admin container, allows SSH access so it can be deployed as a bastion server
###############################################################################
FROM php-base as admin
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"

# semi-colon seperates list of github users that can SSH in
ENV GITHUB_ACCOUNT_SSH_USERS=''

RUN apt-get update && \
    apt-get install -y wget openssh-server sudo netcat default-mysql-client vim telnet && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get autoremove -y

# This doesn't get created automatically, don't know why
RUN mkdir /run/sshd

# Remove password based authentication for SSH
RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config

# Pass environmental variables to SSH sessions
RUN sed -i 's/#PermitUserEnvironment no/PermitUserEnvironment yes/' /etc/ssh/sshd_config

# allow users in the sudo group to do wo without a password
RUN /bin/echo "%sudo ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/no-password-group

COPY docker/admin-entrypoint /entrypoint

# expose the ssh port
EXPOSE 22
ENTRYPOINT /entrypoint

HEALTHCHECK CMD nc -vz 127.0.0.1 22 || exit 1

###############################################################################
# Single purpose container that migrates the databse and then dies
# Should be run once on a new deployment
###############################################################################
FROM php-base as migrate-database
ENTRYPOINT ["/var/www/ilios/bin/console"]
CMD ["doctrine:migrations:migrate", "-n"]

###############################################################################
# Single purpose container to updates the frontend
# Can be run on a schedule as needed and MUST share /var/www/ilios with the
# fpm and nginx containers in order to provide the shared static files that
# have to be in sync
###############################################################################
FROM php-base as update-frontend
ENTRYPOINT ["/var/www/ilios/bin/console"]
CMD ["ilios:update-frontend"]

###############################################################################
# Single purpose container that starts a message consumer
# Should be setup to run and restart itself when it shuts down which it will
# do every hour
###############################################################################
FROM php-base as consume-messages
ENTRYPOINT ["/var/www/ilios/bin/console"]
CMD ["messenger:consume", "async"]

###############################################################################
# MySQL configured as needed for Ilios
###############################################################################
FROM mysql:5.7 as mysql
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
ENV MYSQL_RANDOM_ROOT_PASSWORD yes
COPY docker/mysql.cnf /etc/mysql/conf.d/ilios.cnf
RUN chmod 755 /etc/mysql/conf.d/ilios.cnf

###############################################################################
# Setup a mysql server running the demo database for use in development
###############################################################################
FROM mysql as mysql-demo
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
ENV MYSQL_USER ilios
ENV MYSQL_PASSWORD ilios
ENV MYSQL_DATABASE ilios
ENV DEMO_DATABASE_LOCATION https://s3-us-west-2.amazonaws.com/ilios-demo-db.iliosproject.org/latest_db/ilios3_demosite_db.sql.gz
RUN apt-get update && apt-get install -y wget

COPY docker/fetch-demo-database.sh /fetch-demo-database.sh
RUN /bin/bash /fetch-demo-database.sh

###############################################################################
# Setup elasticsearch with the plugins we needed
###############################################################################
FROM elasticsearch:7.13.1 as elasticsearch
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
RUN bin/elasticsearch-plugin install -b ingest-attachment

###############################################################################
# Our original and still relevant apache based runtime, includes everything in
# a single container
###############################################################################
FROM php:8.0-apache as php-apache
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=src /src /var/www/ilios
#Copy .htaccess files which are not included in src image
COPY ./public/.htaccess /var/www/ilios/public
COPY ./src/.htaccess /var/www/ilios/src

# configure Apache and the PHP extensions required for Ilios and delete the source files after install
RUN \
    apt-get update \
    && apt-get install sudo libldap2-dev libldap-common zlib1g-dev libicu-dev libzip-dev libzip4 unzip -y \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install intl \
    && mkdir -p /usr/src/php/ext/apcu \
    && curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 \
    && docker-php-ext-install apcu \
    && docker-php-ext-enable opcache \
    # enable modules
    && a2enmod rewrite mpm_prefork deflate headers \
    && rm -rf /var/lib/apt/lists/* \
    # remove the apt source files to save space
    && apt-get purge libldap2-dev zlib1g-dev libicu-dev -y \
    && apt-get autoremove -y

COPY ./docker/php.ini $PHP_INI_DIR
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# add our own entrypoint scripts
COPY docker/php-apache-entrypoint /usr/local/bin/

ENV \
COMPOSER_HOME=/tmp \
APP_ENV=prod \
APP_DEBUG=false \
MAILER_DSN=null://null \
ILIOS_LOCALE=en \
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt \
ILIOS_REQUIRE_SECURE_CONNECTION=false \
MESSENGER_TRANSPORT_DSN=doctrine://default

WORKDIR /var/www/ilios
RUN /usr/bin/touch .env
RUN /usr/bin/composer install \
    --prefer-dist \
    --no-dev \
    --no-progress \
    --no-interaction \
    --no-suggest \
    --classmap-authoritative \
    #creates an empty env.php file, real ENV values will control the app
    && /usr/bin/composer dump-env prod \
    && /usr/bin/composer clear-cache

ARG ILIOS_VERSION="v0.1.0"
RUN echo ${ILIOS_VERSION} > VERSION

USER root
RUN chown -R www-data:www-data /var/www/ilios
ENTRYPOINT ["php-apache-entrypoint"]
CMD ["apache2-foreground"]
EXPOSE 80
