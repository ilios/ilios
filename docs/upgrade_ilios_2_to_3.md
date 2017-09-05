
# Upgrading Ilios 2.x to Ilios 3

As Ilios 3 uses an entirely different codebase from Ilios 2.x, only the database needs to be updated when upgrading to version 3 from version 2.x.  With the exception of the folder that contains your Ilios learning materials, which should be backed-up before performing this process, the new Ilios 3 codebase entirely replaces the Ilios 2.x codebase, so you should follow the instructions in [INSTALL.md](https://github.com/ilios/ilios/blob/master/INSTALL.md) to bring the codebase up-to-date.

###*NOTE:* These steps cover upgrading your Ilios database from an Ilios *2.4.8* installation ONLY.###

####*If you are running a version of Ilios earlier than v2.4.8 and would like to upgrade to Ilios 3, you MUST upgrade your Ilios software to release version 2.4.8 BEFORE running these steps! If you do not update to version 2.4.8 before upgrading, your database schema will be incorrect and unusable!*####

Before upgrading your system, you will want to bring down your webserver or redirect users to some kind of 503 maintenance page, so that they will not be making changes to the Ilios database while the upgrade is in-process. We recommend trying this on a development server to test everything out before trying to do it on your production machine.

To upgrade from an already-existing Ilios 2.4.8 installation, perform the following steps in order:

### Steps:

1. Backup all of your learning materials or move them to a location where they will be accessible by the new Ilios 3 installation. *WARNING:* If your learning materials are stored in the default location, be careful to not accidentally delete them when you change your codebase.
2. Backup your current database completely, and do not forget to add the '-R' or'--routines' flags to ensure that your stored procedures and triggers are included in the back up. The command would probably look something like this:

 ```bash
mysqldump -u YOUR_ILIOS_DATABASE_USERNAME -h YOUR_ILIOS_HOSTNAME -R -p YOUR_ILIOS_DATABASE_NAME -r YOUR_DATABASE_BACKUP_FILENAME.sql
```

3. Backup your current database completely! (Yes, this was just mentioned, but it's extremely important that you do not run this update on your production database without a backup!) 
4. Check your current Ilios installation's 'version.php' file to verify that you are currently running version 2.4.8 of the Ilios software.  If you are not running version *2.4.8* specifically , you will need to upgrade to 2.4.8 *BEFORE* continuing with these steps!
5. Checkout the most current release of the Ilios 3 codebase from https://github.com/ilios/ilios/releases (using '/web/ilios3/ilios' for this example)
6. In the newly-checked out directory, navigate to the 'app/Resources' folder where you will find the [updateSchemaFromIlios2toIlios3.sql](https://github.com/ilios/ilios/blob/master/app/Resources/updateSchemaFromIlios2toIlios3.sql) file.
7. Backup your current database completely! (<= That's the 3rd time we've said it! It's probably pretty important!)
8. Apply the sql changes from updateSchemaFromIlios2toIlios3.sql to your database by using the mysql command line client as follows:

 ```bash
mysql -u YOUR_ILIOS_DATABASE_USERNAME -h YOUR_ILIOS_DATABASE_HOSTNAME -p YOUR_ILIOS_DATABASE_NAME < updateSchemaFromIlios2toIlios3.sql
```

*NOTE:* This process could take a while, depending on your the size of your database and the speed of your database server! On a decent database server with an moderate-sized database, this can take up to 20 mins... If everything goes as it should, you will not see ANY status messages on your screen when it completes: it will just return you to another command prompt. If you are doing this on a remote database server, you will want to make sure that your terminal session does not disconnect before the process completes. We recommend using the 'screen' or 'tmux' Linux apps to ensure your terminal session stays connected during this process.

When the above steps are completed, there is one final step to the database migration that must be run from the Symfony console *AFTER* you have completed setting up the Ilios 3 backend.  When you have completed installing the Ilios 3 backend, run the following command from the 'bin/' directory of the new installation's codebase:

The command will look like this:

```bash
sudo -u apache bin/console doctrine:migrations:migrate --env=prod --no-interaction
```
This will apply the final migrations to your database.  All future database updates to Ilios 3 will be take place via this method and this command should be run any time you update your codebase to a later version of Ilios.

For information on setting up the Ilios 3 backend, please see the instructions in the [INSTALL.md](https://github.com/ilios/ilios/blob/master/INSTALL.md) file.

Good job migrating your database, but we're not quite finished yet!

## Migrating your old learning materials and cleaning up the text of your Topics

### Learning Materials
The file storage/naming functionality for handling Learning Materials in Ilios 3 has changed slightly from the way it was done in Ilios 2.x.  In order to support the new formatting and storage, you will need to migrate your learning materials to their new storage location and naming convention.  Thankfully, Ilios 3 provides a console command to do this!

The Learning Material migration *copies* learning materials, it does not *move* them so, before you begin migrating your learning materials, you want to make ABSOLUTELY CERTAIN that you have enough free space in your system's storage (hard disk space) in order to accommodate copying all of your materials to the new location.  At the very least, you will want as much free space as you already use for your learning materials.

To find out how much space your learning materials are currently taking, login to your Ilios server and change directories to the folder that contains your learning materials and then run this command:

 ```bash
df -h . 
```

This is will output something like the following:

 ```bash
Filesystem   Size   Used  Avail Capacity  iused    ifree %iused  Mounted on
/dev/disk1  465Gi  209Gi  255Gi    46% 54901683 66942027   45%   /
```

If the total number of Gigabytes listed under 'Avail' is significantly greater (20Gi+) than what is listed under 'Used,' you should be good to go!  If the 'Avail' value is less (or even just "pretty close") to what is listed under 'Used', you will probably want to increase the storage on your server before running the Learning Material migration.  If you try to run this migration and do not have enough space to store the files when they are copied over, it can fill up your storage space completely and render your server inoperable!  Please be careful and be certain of your storage availability before performing this operation!

If you have plenty of space and believe you are able to migrate your learning materials, do the following:

1. Backup your learning materials!!!
2. Seriously! Back up your learning materials before running this command!!!  If you cannot back up your learning materials or quarantine a copy of them to a separate place on your server where they cannot be affected by this process, you should really consider backing them up offline to some type of external storage device.  Do not risk losing your existing learning materials!
3. Now that you're sure you're ready to migrate, navigate into your Ilios application's root directory (eg, /web/ilios3/ilios) and examine the file at app/config/parameters.yml.  Look for the value that corresponds to file_system_storage_path.  This location to where your learning materials will be copied.
4. Now, check to see where your *CURRENT* Ilios 2 learning materials reside.  If your Ilios 2 installation is at /web/ilios2/htdocs, your current learning material are probably in '/web/ilios2/htdocs/learning_materials'. If this is the case, your learning materials reside in '/web/ilios2/htdocs' -- this is the 'Path to Ilios 2' location that you should enter in the following migration command:

Now, while still in your Ilios application's root directory, run this command:

 ```bash
sudo -u apache bin/console ilios:setup:migrate-learning-materials /web/ilios2/htdocs --env=prod
```

Make sure that you have entered YOUR 'Path to Ilios 2' location and also do not forget to add '--env=prod' to end of the command!  When you hit enter, you learning materials will be copied to the location as listed in your parameters.xml file.

This process can take a while to complete, depending on the number/size of the Learning Materials being copied.

If you would like to verify that your new learning materials are correctly stored, you can run the following command when the migration is complete:

 ```bash
sudo -u apache bin/console ilios:maintenance:validate-learning-materials /web/ilios2/htdocs --env=prod
```

## Cleaning up the text of your Ilios 2.x Topics

One of the biggest complaints of users of Ilios 2.x, regarding the application, was the poor-formatting of the text in item titles and descriptions. While Ilios 2.x did provide a WYSIWYG editor to its users, too many of the users chose to paste text directly into the Ilios text fields from whatever application they were already in (usually MS Word or some other MS Office application), instead of re-entering the text using the in-app editor.  This caused a real problem with the formatting of text in Ilios, so we've updated the handling of text in Ilios 3 and limited the formatting options to only those most-used in Ilios 2.  Doing so, while making new and future text entries format quite nicely, has made the old, over-formatted, pasted-from-Word text format terribly, rendering much of it unreadable.

We have provided a solution to this problem with a fix that will allow you to quickly remove all but only the allowed characters from your item title and description text.  To apply this fix and reformat all your text to only use the proper formatting native to Ilios 3, do the following:

Navigate to your Ilios application root directory (`/web/ilios3/ilios`, for example) and run the following command for each respective text-type:

 ```bash
#for Objective Titles
sudo -u apache bin/console ilios:maintenance:cleanup-strings --objective-title --env=prod
#for Learning Material Descriptions
sudo -u apache bin/console ilios:maintenance:cleanup-strings --learningmaterial-description --env=prod
#for Learning Material Notes
sudo -u apache bin/console ilios:maintenance:cleanup-strings --learningmaterial-note --env=prod
#for Session Descriptions
sudo -u apache bin/console ilios:maintenance:cleanup-strings --session-description --env=prod
```
