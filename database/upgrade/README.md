# Database Upgrade

Run the `upgrade.sql` scripts in subdirectories that apply
to the range of versions that you are upgrading from/to against your Ilios database.
These scripts _must_ be applied in sequential ascending order.

For example - if you are upgrading from version 2.0.5b to version 2.1.0 then you must
apply the following SQL scripts to your Ilios database:

    2.0.6b/upgrade.sql
    2.0.7b/upgrade.sql
    2.1.0/upgrade.sql

_Back up your database first before running any upgrade scripts against it!_
