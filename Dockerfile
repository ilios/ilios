###############################################################################
# Contains all of the ilios src code for use in other containers
###############################################################################
FROM scratch AS src
COPY composer.* symfony.lock LICENSE /src/app/
COPY config /src/app/config/
COPY custom /src/app/custom/
COPY src /src/app/src/
COPY templates /src/app/templates/
COPY migrations /src/app/migrations/
COPY bin/console /src/app/bin/
COPY public/index.php /src/app/public/
COPY public/theme-overrides/ /src/app/public/theme-overrides/

###############################################################################
# Nginx Configured to Run Ilios from an FPM host
###############################################################################
FROM nginx:stable-alpine AS nginx
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY --from=src /src/app /srv/app/
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template

# Setup PHP servers in ENV so we can round robin easily
ENV FPM_CONTAINERS=fpm:9000
# Docker builtin nameserver
ENV NGINX_NAMESERVERS=127.0.0.11

HEALTHCHECK --interval=5s CMD /usr/bin/nc -vz -w1 127.0.0.1 80

###############################################################################
# Dependencies we need in all PHP containers
# Production ready composer pacakges installed
###############################################################################
FROM php:8.4-fpm AS php-base
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=src /src/app /srv/app/

# configure PHP extensions required for Ilios and delete the source files after install
RUN set -eux; \
    apt-get update; \
    apt-get install -y \
        libldap2-dev \
        libldap-common \
        zlib1g-dev \
        libicu-dev \
        libzip-dev \
        libzip4 \
        unzip \
        acl \
        libfcgi-bin; \
    docker-php-ext-configure ldap; \
    docker-php-ext-install ldap; \
    docker-php-ext-install zip; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install intl; \
    mkdir -p /usr/src/php/ext/apcu; \
    curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1; \
    docker-php-ext-install apcu; \
    docker-php-ext-enable apcu; \
    pecl install redis \
    && docker-php-ext-enable redis; \
    docker-php-ext-enable opcache; \
    rm -rf /var/lib/apt/lists/*; \
    rm -rf /tmp/pear; \
    # remove the apt source files to save space
    apt-get purge libldap2-dev zlib1g-dev libicu-dev -y; \
    apt-get autoremove -y;

ENV \
APP_ENV=prod \
APP_DEBUG=false \
ILIOS_DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db" \
ILIOS_FILE_SYSTEM_STORAGE_PATH="/srv/app/var/tmp/ilios-storage/" \
MAILER_DSN=null://null \
ILIOS_LOCALE=en \
ILIOS_SECRET=ThisTokenIsNotSoSecretChangeIt \
ILIOS_REQUIRE_SECURE_CONNECTION=false \
MESSENGER_TRANSPORT_DSN=doctrine://default

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"
WORKDIR /srv/app
RUN /usr/bin/touch .env
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer symfony:dump-env prod; \
    composer run-script --no-dev post-install-cmd; \
    chmod +x bin/console; \
    bin/console cache:warmup; \
    sync
VOLUME /srv/app/var

COPY docker/fpm/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/fpm/ilios.ini $PHP_INI_DIR/conf.d/ilios.ini

RUN ln -sf "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/fpm/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

###############################################################################
# FPM configured to run ilios
# Really just a wrapper around php-base, but here in case we need to modify it
###############################################################################
FROM php-base AS fpm
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY docker/fpm/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --timeout=1s --retries=10 CMD ["docker-healthcheck"]

###############################################################################
# FPM configured for development
# Runs a dev environment and composer dependencies
###############################################################################
FROM fpm AS fpm-dev
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
ENV APP_ENV=dev
ENV APP_DEBUG=true
COPY docker/fpm/symfony.dev.ini $PHP_INI_DIR/conf.d/symfony.ini
RUN ln -sf "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN set -eux; \
    pecl install xdebug \
    && docker-php-ext-enable xdebug; \
    composer install --prefer-dist --no-progress --no-interaction; \
    rm -f .env.local.php; \
    composer run-script post-install-cmd; \
    bin/console cache:warmup; \
    sync

COPY docker/fpm/xdebug-dev.ini $PHP_INI_DIR/conf.d/xdebug.ini

###############################################################################
# Admin container, allows SSH access so it can be deployed as a bastion server
###############################################################################
FROM php-base AS admin
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"

# semi-colon seperates list of github users that can SSH in
ENV GITHUB_ACCOUNT_SSH_USERS=''

RUN apt-get update && \
    apt-get install -y wget openssh-server sudo netcat-traditional default-mysql-client vim telnet && \
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
ENTRYPOINT ["/entrypoint"]

HEALTHCHECK CMD nc -vz 127.0.0.1 22 || exit 1

###############################################################################
# Single purpose container to updates the frontend
# Can be run on a schedule as needed and MUST share /srv/app with the
# fpm and nginx containers in order to provide the shared static files that
# have to be in sync
###############################################################################
FROM php-base AS update-frontend
ENTRYPOINT ["bin/console"]
CMD ["ilios:update-frontend"]

###############################################################################
# Single purpose container that starts a message consumer
# Should be setup to run and restart itself when it shuts down
###############################################################################
FROM php-base AS consume-messages
# add the pcntl extension which allows PHP to consume process controll messages
# and shutdown the message consumer gracefully
RUN set -eux; \
    docker-php-ext-configure pcntl; \
    docker-php-ext-install pcntl;

COPY docker/messages-entrypoint /entrypoint
ENTRYPOINT ["/entrypoint"]

#disable the FPM healthcheck from php-base
HEALTHCHECK NONE

###############################################################################
# Development message consumer
###############################################################################
FROM consume-messages AS consume-messages-dev

ENV APP_ENV=dev
ENV APP_DEBUG=true

COPY docker/fpm/symfony.dev.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/fpm/xdebug-dev.ini $PHP_INI_DIR/conf.d/xdebug.ini

RUN ln -sf "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN set -eux; \
    pecl install xdebug \
    && docker-php-ext-enable xdebug; \
    composer install --prefer-dist --no-progress --no-interaction; \
    rm -f .env.local.php; \
    composer run-script post-install-cmd; \
    bin/console cache:warmup; \
    sync

###############################################################################
# MySQL configured as needed for Ilios
###############################################################################
FROM mysql:8-oracle AS mysql
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
ENV MYSQL_RANDOM_ROOT_PASSWORD=yes
COPY docker/mysql.cnf /etc/mysql/conf.d/ilios.cnf
RUN chmod 755 /etc/mysql/conf.d/ilios.cnf

###############################################################################
# Setup a mysql server running the demo database for use in development
###############################################################################
FROM mysql AS mysql-demo
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
ENV MYSQL_USER=ilios
ENV MYSQL_PASSWORD=ilios
ENV MYSQL_DATABASE=ilios
ENV DEMO_DATABASE_LOCATION=https://ilios-demo-db.iliosproject.org/
RUN set -eux; \
    microdnf install -y wget; \
    microdnf clean all;
COPY docker/fetch-demo-database.sh /fetch-demo-database.sh
RUN /bin/bash /fetch-demo-database.sh

###############################################################################
# Setup opensearch with the plugins we needed
###############################################################################
FROM opensearchproject/opensearch:2 AS opensearch
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
RUN bin/opensearch-plugin install -b ingest-attachment

###############################################################################
# Setup redis with needed config
###############################################################################
FROM redis:7-alpine AS redis
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY docker/redis/redis.conf /usr/local/etc/redis/redis.conf
CMD [ "redis-server", "/usr/local/etc/redis/redis.conf" ]

###############################################################################
# Create our own tika, so we can customize it if needed
###############################################################################
FROM apache/tika AS tika
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"

###############################################################################
# Our original and still relevant apache based runtime, includes everything in
# a single container
###############################################################################
FROM php:8.4-apache AS php-apache
LABEL maintainer="Ilios Project Team <support@iliosproject.org>"
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=src /src/app /var/www/ilios
#Copy .htaccess files which are not included in src image
COPY ./public/.htaccess /var/www/ilios/public
COPY ./src/.htaccess /var/www/ilios/src

# configure Apache and the PHP extensions required for Ilios and delete the source files after install
RUN set -eux; \
    apt-get update; \
    apt-get install acl libldap2-dev libldap-common zlib1g-dev libicu-dev libzip-dev libzip4 unzip -y; \
    docker-php-ext-configure ldap; \
    docker-php-ext-install ldap; \
    docker-php-ext-install zip; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install intl; \
    mkdir -p /usr/src/php/ext/apcu; \
    curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1; \
    docker-php-ext-install apcu; \
    docker-php-ext-enable opcache; \
    pecl install redis \
    && docker-php-ext-enable redis; \
    # enable modules
    a2enmod rewrite mpm_prefork deflate headers; \
    rm -rf /var/lib/apt/lists/*; \
    rm -rf /tmp/pear; \
    # remove the apt source files to save space
    apt-get purge libldap2-dev zlib1g-dev libicu-dev -y; \
    apt-get autoremove -y;

COPY ./docker/php.ini $PHP_INI_DIR
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

# add our own entrypoint scripts
COPY docker/php-apache-entrypoint /usr/local/bin/

ENV \
COMPOSER_HOME=/tmp \
COMPOSER_ALLOW_SUPERUSER=1 \
APP_ENV=prod \
APP_DEBUG=false \
MAILER_DSN=null://null \
ILIOS_DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db" \
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
    && composer run-script --no-dev post-install-cmd

USER root
ENTRYPOINT ["php-apache-entrypoint"]
CMD ["apache2-foreground"]
EXPOSE 80
