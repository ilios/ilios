# Installing PHPUnit

You will need to install PHPUnit and DbUnit. We recommend doing so via [Composer](http://getcomposer.org).

```bash
cd <iliosroot>/tests/phpunit
composer install
```

See the [official manual](http://phpunit.de/manual/current/en/installation.html#installation.composer) for more details.

# Configuring the test environment

## Common steps

1. In the `tests/phpunit` directory, copy `default.phpunit.xml` to `phpunit.xml`.

## Unit tests

No additional configuration steps are required.

## End-to-end tests

For full end-to-end testing, an Ilios database is expected to be present and accessible.

See the following additional configuration steps:

1. Set up a new Ilios database `ilios_test` using the scripts in the `/database/install` directory.
2. Edit `phpunit.xml` to customize it for your environment.
Note that the value for `ILIOS2_TEST_DB_ACTIVE_GROUP` will be the name of a new DB group that you will create in the next step.
```xml
<!--  name of the CI db group configuration which should be used for testing -->
<const name="ILIOS2_TEST_DB_ACTIVE_GROUP" value="ilios_test" />
```

3. Add a new DB group named `ilios_test` to your `application/config/database.php` file. Point it to the new `ilios_test` database that you created earlier, then set `$active_group` to `ilios_test`.

```php
<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = "ilios_test";
$active_record = TRUE;

$db['ilios_test']['hostname'] = 'localhost';
$db['ilios_test']['username'] = 'ilios_user';
$db['ilios_test']['password'] = 'XXXXXX';
$db['ilios_test']['database'] = 'ilios_test';
$db['ilios_test']['dbdriver'] = 'mysqli';
$db['ilios_test']['dbprefix'] = '';
$db['ilios_test']['pconnect'] = TRUE;
$db['ilios_test']['db_debug'] = TRUE;
$db['ilios_test']['cache_on'] = FALSE;
$db['ilios_test']['cachedir'] = '';
$db['ilios_test']['char_set'] = 'utf8';
$db['ilios_test']['dbcollat'] = 'utf8_general_ci';
$db['ilios_test']['swap_pre'] = '';
$db['ilios_test']['autoinit'] = TRUE;
$db['ilios_test']['stricton'] = FALSE;
```

## Integration tests

_Relevant to UCSF only!_

Ilios provides tests for its built-in LDAP client that is used to query UCSF's campus directory (EDS).

In order to run these, you will need to provide actual values for the various `ILIOS_TEST_USER_SYNC_EDS_*`
configuration options defined in the phpunit configuration file.


```xml
<!--  LDAP/EDS configuration for user sync process tests -->
<const name="ILIOS_TEST_USER_SYNC_EDS_BIND_DN" value="XXXXX" />
<const name="ILIOS_TEST_USER_SYNC_EDS_PASSWORD" value="XXXXX" />
<const name="ILIOS_TEST_USER_SYNC_EDS_HOST" value="ldaps://XXXXX" />
<const name="ILIOS_TEST_USER_SYNC_EDS_PORT" value="XXXXX" />
```

# Running tests

## Unit tests

Exclude the end-to-end and integration tests:

```bash
cd <iliosroot>/tests/phpunit
bin/phpunit --exclude-group ede,integration Ilios
```

## End-to-end tests

**ACHTUNG!**  Do not run this these tests against your regular development, stage or production database, otherwise it will get clobbered.

Only run tests in the `ede` group.

```bash
cd <iliosroot>/tests/phpunit
bin/phpunit  --group ede Ilios
```

##  Integration tests

Only run tests in the `integration` group.

```bash
cd <iliosroot>/tests/phpunit
bin/phpunit  --group integration Ilios
```
