# Installation

## Requirements

The Ilios has the following minimal technical requirements

* `Apache 2.2.3+` or `IIS 6+`

* `PHP 5.3.3+`, with `mysqli` support.

    **Note**: short tags must be turned on in the PHP configuration (`short_open_tag = On`).

    It is recommended that you allocate at least 128 MB of memory to PHP for script execution (`memory_limit = 128M`)

* `MySQL 5.0.77` or later, with support for `InnoDB` and `MyISAM`

    **Note:** MySQL must NOT run in `strict` SQL mode.

## Deployment

The minimal steps to get an Ilios deployment up-and-running can be described as the following:

1. Download and extract the distribution tarball.

2. Copy the content of the `/web` directory to your target deployment directory.

3. In your deployment directory, change file permissions on the following directories to make them writeable by the process that runs your web server:

        learning_materials/
        tmp_uploads/
        system/logs

4. In your deployment directory, rename the following files:

        default.index.php ->  index.php
        system/application/config/default.config.php -> system/application/config/config.php
        system/application/config/default.ilios.php -> system/application/config/ilios.php
        system/application/config/default.database.php -> system/application/config/database.php

5. In your deployment directory, configure the following files to reflect your institution's name, appropriate URLs, and database attributes:

        index.php                               ... substitute placeholder token with your URL
        system/application/config/config.php    ... substitute placeholder token with your URL
        system/application/config/ilios.php     ... set your institution's name and authentication method
        system/application/config/database.php  ... fill in your database connection settings

6. Construct and populate your database as described in `database/install/README.md`.

7. Run the `database/install/install_user_zero.sh` script to create the default administrator account.

# Security and Authentication

## Ilios-internal Authentication

If you are setting up an Ilios instance from scratch, it is highly recommended that you provide a salt to increase the security of user passwords.
You may do so by assigning a value to the  `$config['ilios_authentication_internal_auth_salt']` setting in `system/application/config/ilios.php`.


## Shibboleth Authentication

In `system/application/config/ilios.php`, change the authentication method to "shibboleth".

    $config['ilios_authentication'] = 'shibboleth';

We recommend the following exposure scheme; this is assuming Ilios2 is installed at the web-root:

    <Location />
      AuthType shibboleth
      ShibRequestSetting requireSession 1
      require valid-user
    </Location>

    <LocationMatch /$>
      Satisfy Any
      Allow from all
    </LocationMatch>

    <LocationMatch "/ilios2.php/([^/]+)/getI18NJavascriptVendor">
      Satisfy Any
      Allow from all
    </LocationMatch>

    <Location /index.php>
      Satisfy Any
      Allow from all
    </Location>

    <Location /images>
      Satisfy Any
      Allow from all
    </Location>

    <Location /system/application/views/scripts>
      Satisfy Any
      Allow from all
    </Location>

    <Location /system/application/views/images>
      Satisfy Any
      Allow from all
    </Location>

    <Location /system/application/views/css>
      Satisfy Any
      Allow from all
    </Location>

    <Location /favicon.ico>
      Satisfy Any
      Allow from all
    </Location>

    <Location /version.php>
      Satisfy Any
      Allow from all
    </Location>
