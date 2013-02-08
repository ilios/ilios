# Installation

## Requirements

The Ilios has the following minimal technical requirements

* `Apache 2.2.3+` or `IIS 6+`

* `PHP 5.3.3+`, with `mysqli` support.

    It is recommended that you allocate at least 128 MB of memory to PHP for script execution (`memory_limit = 128M`)

* `MySQL 5.0.77` or later, with support for `InnoDB` and `MyISAM`

    **Note:** Your MySQL server must NOT run in `strict` SQL mode. More information on detecting and setting server SQL-modes can be found in the [MySQL online documentation](http://dev.mysql.com/doc/refman/5.1/en/server-sql-mode.html).

## Deployment

The minimal steps to get an Ilios deployment up-and-running can be described as the following:

1. Download and extract the distribution tarball.

2. Copy the content of the `/web` directory to your target deployment directory.

3. In your deployment directory, change file permissions on the following directories to make them writeable by the process that runs your web server:

        learning_materials
        tmp_uploads
        application/cache
        application/logs

4. In your deployment directory, rename the following files:

        default.index.php ->  index.php
        application/config/default.config.php -> application/config/config.php
        application/config/default.ilios.php -> application/config/ilios.php
        application/config/default.database.php -> application/config/database.php

5. In your deployment directory, configure the following files to reflect your institution's name, appropriate URLs, and database attributes:

        index.php                        ... substitute placeholder tokens with a version string
        application/config/config.php    ... substitute placeholder token with your URL
        application/config/ilios.php     ... set your institution's name and authentication method
        application/config/database.php  ... fill in your database connection settings

6. Construct and populate your database as described in `database/install/README.md`.

7. Run the `database/install/install_user_zero.sh` script to create the default administrator account.

# Security and Authentication

## Session Encryption

It is recommended to change the default value of the `$config['encryption_key']` setting in `application/config/config.php`.
Read the "Setting your Key" section in the [CodeIgniter User Guide](http://ellislab.com/codeigniter/user-guide/libraries/encryption.html) for a further discussion on best-practices for choosing a secure encryption key value.

## Ilios-internal Authentication

If you are setting up an Ilios instance from scratch, it is highly recommended that you provide a salt to increase the security of user passwords.  
You may do so by assigning a value to the  `$config['ilios_authentication_internal_auth_salt']` setting in `application/config/ilios.php`.

## Shibboleth Authentication

In `application/config/ilios.php`, change the authentication method to "shibboleth".

    $config['ilios_authentication'] = 'shibboleth';

We recommend the following exposure scheme; this is assuming Ilios is installed at the web-root:

    <Location />
      AuthType shibboleth
      ShibRequestSetting requireSession 1
      require valid-user
    </Location>

    <LocationMatch /$>
      Satisfy Any
      Allow from all
    </LocationMatch>

    <LocationMatch "/ilios.php/([^/]+)/getI18NJavascriptVendor">
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

    <Location /application/views/scripts>
      Satisfy Any
      Allow from all
    </Location>

    <Location /application/views/images>
      Satisfy Any
      Allow from all
    </Location>

    <Location /application/views/css>
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
