# Ilios 3 Installation Instructions

This documentation covers new Ilios installations for users that have never had any current or prior version of Ilios running on their server. If you are looking to upgrade a previous installation of Ilios, please see [Upgrading Ilios 2.x to Ilios 3](UPGRADE.md).

## Ilios 3 - Frontend vs. Backend

Ilios 3 uses a [Symfony](https://symfony.com/) ([PHP](https://php.net))/SQL backend that provides an API to the Ilios 3 Frontend that is built in javascript using [EmberJs](https://emberjs.com).  The Ilios 3 Frontend is managed by the [Ilios development team](https://iliosproject.org) and the latest frontend javascript is served to users via the [Amazon Cloudfront](http://aws.amazon.com/cloudfront/) CDN (Content Delivery Network).

## Frontend 
The Ilios Frontend is always up-to-date on our content-delivery servers at Amazon S3, but you will need to regularly run a console command on your backend instance in order ensure that your users are seeing the latest version and that they are getting all the latest features and bugfixes. To make sure you users are receiving the latest frontend code at all times, just follow these steps:

```bash
# Log in to your Ilios API (backend) server and go to the root folder of your Ilios application ('/web/ilios3' for this example)
$ cd /web/ilios3

# Then run the 'ilios:maintenance:update-frontend' console command, in the context of your the user that runs your webservices (eg. 'apache')
$ sudo -u apache bin/console ilios:maintenance:update-frontend --env=prod
Frontend updated successfully!

# and that's it!
```
It is a good idea to run this command often (in a cron job, perhaps!) and after any backend update as well, of course!

Though you will probably never need to deploy the Ilios 3 Frontend on your own servers, the source for the frontend code can always be found at https://github.com/ilios/frontend.

Currently, there is no Frontend deployment documentation available so, if you feel that you must manage the frontend files yourself, please contact the [Ilios Project Support Team](https://iliosproject.org) at support@iliosproject.org or refer back to the Ilios Frontend [README.md](https://github.com/ilios/frontend/blob/master/README.md) file at a later time for instructions on how to do so.

## Backend
To build/deploy the Ilios 3 backend, you will need to install the default Ilios database schema and then follow the steps for deploying code to your webserver(s) and configuring them to connect to the database and serve the API.

## Pre-requisites/requirements
As mentioned above, Ilios 3 uses a Symfony (PHP/SQL) backend to serve its API, so these tools and their required dependencies need to be installed before you can install the application itself. Here at the Ilios Project, we currently run and recommend running Ilios 3 using a "LAMP" (Linux Apache MySQL PHP) technology stack with the following software packages and versions:

* CentOS 7 - Any modern Linux should work, but we recommend Redhat (RHEL, CentOS, or Fedora) or Ubuntu
* MySQL using the InnoDB database engine (v5.5 or later required, 5.6+ recommended)
* PHP v5.6 or later required, 7.0+ recommended (available for CentOS and RHEL from https://ius.io/). While [PHP v5.6 will still receive security updates until December 2018, it is now technically EOL (End Of Life)](http://php.net/supported-versions.php) and Ilios will no longer run on PHP versions earlier than 7.0 after July 1st, 2017.

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

### URL Rewriting
Users should enable URL-rewriting on their webserver if at all possible. For those using Apache, this can be done by installing and enabling the 'mod_rewrite' module. In IIS, this is handled via the [Microsoft IIS URL Rewrite extension](https://www.iis.net/downloads/microsoft/url-rewrite)

You are also need the Composer PHP package management tool.  If you do not have it, you can learn about it and download it at https://getcomposer.org

NOTE: Several institutions have successfully deployed Ilios using Microsoft IIS on Windows as their webserver, but we do not recommend it as we do not have alot of experience with it ourselves and we've only ever support Ilios on Linux systems.  That being said, if you MUST use IIS for Windows and are having trouble getting Ilios running properly, please contact the [Ilios Project Support Team](https://iliosproject.org) at support@iliosproject.org if you have any problems and we might be able to help you out!

### Code Deployment

These steps assume that you are deploying the Ilios 3 backend via a Git clone from the Ilios repository at [https://github.com/ilios/ilios.git](https://github.com/ilios/ilios.git) and building the install using PHP's 'composer' package manager. For installation from the Ilios 3 source distribution files, please refer to Installing the Ilios 3 backend from distribution file.

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
5. Run the following command to build the packages and its dependencies.  This step assumes you have PHP 5.6+ and Composer installed on your system:
```bash
sudo -u apache SYMFONY_ENV=prod composer install --no-dev --optimize-autoloader
```  
This will install the required PHP Symfony packages and their dependencies.  When the process nears completion, you will be prompted with the following configuration setting options.  You should set them as noted:
```bash
#default value, set to your value if different
database_driver: pdo_mysql
 
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
 
# Authenticaion_type can currently be 'form' (default),'ldap', 'shibboleth', and 'cas' depending on your institution
authentication_type: form
 
#legacy_password_salt should be your old Ilios2 password salt, if upgrading from ilios 2, otherwise, leave as 'null' (default):
legacy_password_salt: null
 
#file_system_storage_path is where you plan to put your uploadable files, like learning materials
file_system_storage_path: /web/ilios3/file_uploads
 
# Curriculum Inventory Reporting:
# This is the name of the institution as it will appear in the Curriculum
# Inventory Export
institution_domain: ucsf.edu
 
# This should be the link to where your Curriculum Inventory Export may
# be viewed:
supporting_link: null
 
# if you are using LDAP to authenticate users logging into Ilios, you should
# fill these in, otherwise leave 'null'
ldap_authentication_host: null
ldap_authentication_port: null
ldap_authentication_bind_template: null

# if you connect to a directory to pull user info and attributes via an LDAP
# connection, fill in these LDAP values. Otherwise leave null
ldap_directory_url: null
ldap_directory_user: null
ldap_directory_password: null
ldap_directory_search_base: null
ldap_directory_campus_id_property: null
ldap_directory_username_property: null
 
# if you use Shibboleth to authenticate users logging into Ilios, you should
# fill in the appropriate values here, otherwise null or default values 
# should be fine
shibboleth_authentication_login_path: /Shibboleth.sso/Login
shibboleth_authentication_logout_path: /Shibboleth.sso/Logout
shibboleth_authentication_user_id_attribute: eppn

# Setting the appropriate timezone value is VERY IMPORTANT.
# Set this value to the appropriate timezone as selected from 
# the list at http://php.net/manual/en/timezones.php
timezone: America/Los_Angeles

# The Ilios Project always recommends you only connect to your Ilios
# application over https, but you may need to set this value to force 'http
# connections if your configuration requires it (behind an already-encrypted
# load-balancer, etc)
forceProtocol: https
  
# Keeping the frontend updated ensures that you will receive the latest
# frontend whenever you run the 'ilios:maintenance:update-frontend' console
# command.
keep_frontend_updated: true
 
# Enter the following values only if you are using CAS to authenticate 
# users logging into Ilios, otherwise leave default or null
cas_authentication_server: null
cas_authentication_version: 3
cas_authentication_verify_ssl: true
cas_authentication_certificate_path: null
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

If you are already running a 2.x version of Ilios, you can upgrade your current database by following the intructions at [Upgrading Ilios 2.x to Ilios 3](UPGRADE.md).

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
# clear the cache items residing on the filesystem...
sudo -u apache rm -Rf var/cache/*
# then clear the Symfony cache...
sudo -u apache bin/console cache:clear --env=prod
# and finally, the APC cache (by restarting the httpd server)
sudo service httpd restart
```
## And that's it!  You should now have a working Ilios 3 backend!

To get started with your new version of Ilios, you are going to have to create the 'first user' in order to get into the application. The username for this user will always be 'first_user', the password will be 'Ch4nge_m3', and the user's role will be set as 'Course Director', which will allow you to perform all necessary tasks to start administering the application.

To add the first user, we need to return to the the Ilios application root folder ('/web/ilios3/ilios') and get ready to run the following console command.  Before running the command, however, you'll want to gather this information about the user you intend to add:

* The user's email address (eg, ilios_admin@example.edu)
* The id of the school that user will be part of; this will most likely be '1' (School of Medicine)

You will be prompted for the email address and school id info when you run the following command (And don't worry, you can change this info after you login!):

```bash
sudo -u apache bin/console ilios:setup:first-user --env=prod 
```

After that, visit the url of your new Ilios 3 site and you should see a login form.  Enter 'first_user' in the login field and 'Ch4nge_m3' (without the quotes!) in the password field and submit!

If everything went correctly, you're ready to go!  Congratulations on installing Ilios!

Please feel free to contact us support@iliosproject.org if you have any questions, comments, or suggestions!
