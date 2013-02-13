# Database Upgrade

## Version 2.2 and below

Run the `upgrade.sql` scripts in subdirectories that apply
to the range of versions that you are upgrading from/to against your Ilios database.
These scripts _must_ be applied in sequential ascending order.

For example - if you are upgrading from version 2.0.5b to version 2.1.0 then you must
apply the following SQL scripts to your Ilios database:

    2.0.6b/upgrade.sql
    2.0.7b/upgrade.sql
    2.1.0/upgrade.sql

_Back up your database first before running any upgrade scripts against it!_

## Version 2.3 and newer

Starting with Ilios v2.3, database upgrades have been integrated into the application itself.
They can be executed by invoking the `migrate` controller's default action.
To deploy the latest pending upgrades to your database, open up a command-line terminal, change directories to your application root and run

    php ilios.php migrate

Note: Execute this command _after_ you have deployed the latest code to your application root. And as always - back up your database before upgrading.

