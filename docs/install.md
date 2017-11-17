# Ilios 3 Installation Instructions

This documentation covers new Ilios installations for users that have never had any current or prior version of Ilios running on their server.

If you are looking to update your installation of Ilios, please see [Updating Ilios 3.x](update.md).

If you are looking to upgrade a previous v2.x installation of Ilios, please see [Upgrading From Ilios 2.x](upgrade_ilios_2_to_3.md).

### Summary
To build/deploy the Ilios 3 backend, you will need to install the default Ilios database schema and then follow the steps for deploying code to your webserver(s) and configuring them to connect to the database and serve the API.

## Pre-requisites/requirements
Ilios 3 uses a Symfony (PHP/SQL) backend to serve its API, so these tools and their required dependencies need to be installed before you can install the application itself. Here at the Ilios Project, we currently run and recommend running Ilios 3 using a "LAMP" (Linux Apache MySQL PHP) technology stack with the following software packages and versions:

* CentOS 7 - Any modern Linux should work, but we recommend Redhat (RHEL, CentOS, or Fedora) or Ubuntu
* MySQL using the InnoDB database engine (v5.5 or later required, 5.6+ recommended)
* PHP v7.0+ REQUIRED, v7.1 RECOMMENDED - In order to ensure the best security and performance of the application overall, we have adopted a policy of requiring the latest version of PHP for running Ilios. Please see [ilios_php_version_policy.md](docs/ilios_php_version_policy.md) for the latest information about the PHP version requirements for Ilios.

NOTE: Several institutions have successfully deployed Ilios using Microsoft IIS on Windows as their webserver, but we do not recommend it as we do not have alot of experience with it ourselves and we've only ever support Ilios on Linux systems.  That being said, if you MUST use IIS for Windows and are having trouble getting Ilios running properly, please contact the [Ilios Project Support Team](https://iliosproject.org) at support@iliosproject.org if you have any problems and we might be able to help you out!

PHP should configured with a 'memory_limit' setting of at least 386MB and have the following required packages/modules/extensions enabled:

* php-mbstring - for UTF-8 support
* php-ldap - for ldap-connectivity support (when using LDAP)
* php-xml 
* php-domxml
* php-pecl-apcu - caching
* php-mysql - DB connectivity
* php-mysqlnd - DB connectivity
* php-pdo - DB connectivity
* php-zip - for native zip package [de]compression during the composer installation process

##### A note about SELinux
 
CentOS, RedHat, and Fedora Linux distributions come with SELinux installed and enabled by default.  SELinux, aka "Security-Enhanced Linux", greatly limits many actions typically allowed out-of-the-box on most Linux distros so, if you seem to be having issues with your Ilios installation not working correctly and you have SELinux installed and enabled, we recommend you review your SELinux settings and/or check out our [Troubleshooting](#troubleshooting) section below.

### URL Rewriting
You must enable URL-rewriting on your webserver. For those using Apache, this can be done by installing and enabling the 'mod_rewrite' module. In IIS, this is handled via the [Microsoft IIS URL Rewrite extension](https://www.iis.net/downloads/microsoft/url-rewrite)

### Composer
You will need the Composer PHP package management tool.  If you do not have it, you can learn about it and download it at https://getcomposer.org

### Code Deployment

These steps assume that you are deploying the Ilios 3 backend via a Git clone from the Ilios repository at [https://github.com/ilios/ilios.git](https://github.com/ilios/ilios.git) and building the install using PHP's 'composer' package manager.

All the steps below should be performed in the context of the user that runs your webserver process (typically, 'apache')

1. Log into your webserver(s) and change to your web server root ('/web/ilios3' for this example).
2. Clone/update Ilios 3 directory tree to this directory (assuming 'git clone' for this example).
```bash
sudo -u apache git clone https://github.com/ilios/ilios.git
```
This will create a folder named 'ilios' in your server root directory.  The entire application source tree will be downloaded to this folder and when this process is finished, the 'web' subfolder should be set as your web server's document root ('/web/ilios3/ilios/web') in your webserver configuration.  
3. Change into the newly created folder

```bash
cd ilios
```
You should now be in the '/web/ilios3/ilios' directory  
4. Checkout the correct release version, based on its release tag ('v3.0.0' for this example):
```bash
sudo -u apache git checkout tags/v3.0.0
```   
5. Run the following command to build the packages and its dependencies.  This step assumes you have PHP 7.0+ and Composer installed on your system:
```bash
sudo -u apache SYMFONY_ENV=prod composer install --no-dev --optimize-autoloader
```  
This will install the required PHP Symfony packages and their dependencies.  When the process nears completion, you will be prompted with the following configuration setting options.  You should set them as noted:
```bash
#Set this to your database host's IP address or hostname
database_host: 127.0.0.1
 
#3306 is the default for MySQL but your db port may be different
database_port: 3306
 
#enter the name of your database where your Ilios data resides 
database_name: ilios
 
#enter the name of the user login name you use for accessing the database 
database_user: ilios_user
 
#this should be set to YOUR database password
database_password: 3x@mp73P@$$w0rd
 
# Set the version of your MySQL database server software
database_mysql_version: 5.6
 
#default values, set to your values if different
mailer_transport: smtp
mailer_host: 127.0.0.1
mailer_user: null
mailer_password: null
 
#en = 'english', enter your desired language ISO abbreviation, if different
locale: en
 
#The value for 'secret' should be a long string of random characters and letters of your choosing.  If you are 
#running in a load-balanced environment, these need to be the same on all the webservers in
#the group. IMPORTANT:  You MUST change this to your own value as using the default value would
#be EXTREMELY dangerous in a production enviroment!
secret: ThisCanBeWhateverYouLike,JustMakeSureYouChangeIt!
```

That should complete the first-time configuration of the parameters.yml file.  If you need to update these values, you can do so at anytime by editing the file directly, but you will need to clear the Symfony cache after making any changes by running the following console command from the root of the Ilios application:
```
sudo -u apache bin/console cache:clear --env=prod
```


6. Lastly, update the auto-loader to account for any new classes/classmap packages:
```
sudo -u apache SYMFONY_ENV=prod composer dump-autoload --no-dev --classmap-authoritative
```

Congratulations! Once you've the completed the above steps, the latest codebase will have been built and deployed!

## Database

### Upgrading from previous versions of Ilios

If you are already running a 2.x version of Ilios, you can upgrade your current database by following the intructions at [Upgrading From Ilios 2.x](docs/upgrade_ilios_2_to_3.md).

### Creating a new database for Ilios
If you are NOT upgrading from a previous version of Ilios, you can create a new, empty database schema by using the following Symfony console command:

```bash
bin/console doctrine:database:create --env=prod
bin/console doctrine:migrations:migrate --env=prod
bin/console doctrine:fixtures:load --env=prod
```

This will create your database schema, with all tables and constraints, and will also load in all the default lookup table data, like competencies and topics --- which you can modify once you're done with setup --- but it won't have any course data or any other unique data about your specific school or curriculum until you log in and add some.


* Finally, you should clear all cached items from the system, including the Symfony Cache (and its store on the filesystem) and the APC cache:
```bash
# clear the Symfony cache...
sudo -u apache bin/console cache:clear --env=prod
# and finally, the APC cache (by restarting the httpd server)
sudo service httpd restart
```
**And that's it!  You should now have a working Ilios 3 backend!**

## Setup a First User

To get started with your new version of Ilios, you are going to have to create the 'first user' in order to get into the application. The username for this user will be 'first_user', the password will be 'Ch4nge_m3', and the user's role will be set as 'Course Director', which will allow you to perform all necessary tasks to start administering the application.

To add the first user, we need to return to the the Ilios application root folder ('/web/ilios3/ilios') and get ready to run the following console command.  Before running the command, however, you'll want to gather this information about the user you intend to add:

* The user's email address (eg, ilios_admin@example.edu)
* The id of the school that user will be part of; this will most likely be '1' (School of Medicine)

You will be prompted for the email address and school id info when you run the following command (And don't worry, you can change this info after you login!):

```bash
sudo -u apache bin/console ilios:setup:first-user --env=prod 
```

After that, visit the url of your new Ilios 3 site and you should see a login form.  Enter 'first_user' in the login field and 'Ch4nge_m3' (without the quotes!) in the password field and submit!

If everything went correctly, you're ready to go!  Congratulations on installing Ilios! If not, please see some of our troubleshooting suggestions below.

Make sure to take a look at [update](update.md) for information on staying up to date and
[authentication](authentication.md) for help setting up ilios to use your campus single sign on system.

## <a name="troubleshooting"></a>Troubleshooting

If you've followed all of the instructions in this document and are still unable to get Ilios work properly, here are some things to try and/or verify:

1. Using SELinux? - If you are attempting to run Ilios on a system that has SELinux enabled and running, you may need to run the following commands in order to allow your webserver to connect to your database:
 ```bash
 setsebool -P httpd_can_network_connect 1
 setsebool -P httpd_can_network_connect_db 1
 ```
2. If you are making changes to your parameters.yml configuration file and your changes do not seem to be taking effect, remember to clear your Symfony cache any time you make a change to the file. This command must be run from the root directory of your Ilios application, in the context of the user running your web services (eg, 'apache' or 'nginx'):
```bash
sudo -u apache bin/console cache:clear --env=prod
```

If you have tried the above steps without any luck, of if you think we should add another troubleshooting suggestion/solution, please feel free to contact us at support@iliosproject.org if you have any questions, comments, or suggestions!
