# Installing PHPUnit
 
You will need to install PHPUnit (version 3.6.10 or later) and DbUnit
 
```
sudo pear config-set auto_discover 1
sudo pear install pear.phpunit.de/PHPUnit
sudo pear install phpunit/DbUnit
```
 
See the [official manual](http://www.phpunit.de/manual/current/en/installation.html#installation.pear) for more details.
 
# Configuring the test environment
 
1. In the `tests/phpunit` directory, copy `sample.phpunit.xml` to `phpunit.xml`. 
2. Set up a new Ilios database `ilios_test` using the scripts in the `/database/install` directory.
3. Edit `phpunit.xml` to customize it for your environment.   
Note that the value for `ILIOS2_TEST_DB_ACTIVE_GROUP` will be the name of a new DB group that you will create in the next step.
```xml
<!--  name of the CI db group configuration which should be used for testing -->
<const name="ILIOS2_TEST_DB_ACTIVE_GROUP" value="ilios_test" />
```

4. Add a new DB group named `ilios_test` to your `application/config/database.php` file. Point it to the new `ilios_test` database that you created earlier, then set `$active_group` to `ilios_test`.
 
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

**ACHTUNG!**
Do not run this these tests against your regular development, stage or production database, otherwise it will get clobbered.
 
# Running tests
 
Change to @tests/phpunit@ directory and run this command:
 
```
$ phpunit .
```
