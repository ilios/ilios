# Upgrading Ilios 2.x to Ilios 3

As Ilios 3 uses an entirely different codebase from Ilios 2.x, only the database needs to be updated when upgrading to version 3 from version 2.x.  With the exception of the folder that contains your Ilios learning materials, which should be backed-up before performing this process, the new Ilios 3 codebase entirely replaces the Ilios 2.x codebase, so you should follow the instructions in [INSTALL.md](https://github.com/ilios/ilios/blob/master/INSTALL.md) to bring the codebase up-to-date.

###*NOTE:* These steps cover upgrading your Ilios database from an Ilios *2.4.8* installation ONLY.###

####*If you are running a version of Ilios earlier than v2.4.8 and would like to upgrade to Ilios 3, you MUST upgrade your Ilios software to release version 2.4.8 BEFORE running these steps! If you do not update to version 2.4.8 before upgrading, your database schema will be incorrect and unusable!*####

Before upgrading your system, you will want to bring down your webserver or redirect users to some kind of 503 maintenance page, so that they will not be making changes to the Ilios database while the upgrade is in-process.

To upgrade from an already-existing Ilios 2.4.8 installation, perform the following steps in order:

### Steps:

1. Backup all of your learning materials or move them to a location where they will be accessible by the new Ilios 3 installation. *WARNING:* If your learning materials are stored in the default location, be careful to not accidentally delete them when you change your codebase.
2. Backup your current database completely, and do not forget to add the '-R' or'--routines' flags to ensure that your stored procedures and triggers are included in the back up. The command would probably look something like this:

```bash
mysqldump -u YOUR_ILIOS_DATABASE_USERNAME -h YOUR_ILIOS_HOSTNAME -R -p YOUR_ILIOS_DATABASE_NAME -r YOUR_DATABASE_BACKUP_FILENAME.sql
```

1. Backup your current database completely! (Yes, this was just mentioned, but it's extremely important that you do not run this update on your production database without a backup!) 
2. Check your current Ilios installation's 'version.php' file to verify that you are currently running version 2.4.8 of the Ilios software.  If you are not running version *2.4.8* specifically , you will need to upgrade to 2.4.8 before continuing.
3. Checkout the most current release of the Ilios 3 codebase from https://github.com/ilios/ilios/releases (using '~/ilios' for this example)
4. In the newly-checked out directory, navigate to the 'app/Resources/migrations' folder where you will find the [fromIlios2.sql](https://github.com/ilios/ilios/blob/master/app/Resources/migrations/fromIlios2.sql) file.
5. Backup your current database completely! (<= That's the 3rd time we've said it! It's probably pretty important!)
6. Apply the sql changes from fromIlios2.sql to your database by using the mysql command line client as follows:

```bash
mysql -u YOUR_ILIOS_DATABASE_USERNAME -h YOUR_ILIOS_DATABASE_HOSTNAME -p YOUR_ILIOS_DATABASE_NAME < fromIlios2.sql
```

*NOTE:* This process could take a while, depending on your the size of your database and the speed of your database server! On a decent database server with an moderate-sized database, this can take up to 20 mins... If everything goes as it should, you will not see ANY status messages on your screen when it completes: it will just return you to another command prompt. If you are doing this on a remote database server, you will want to make sure that your terminal session does not disconnect before the process completes.

When these steps are completed, your database will be ready to use with the Ilios 3 backend. It will NO LONGER WORK with any version of Ilios 2.x.

For information on setting up the Ilios 3 backend, please see the instructions in the [INSTALL.md](https://github.com/ilios/ilios/blob/master/INSTALL.md) file.
