# Installation Instructions

## Ilios 3 - Frontend vs. Backend

Ilios 3 uses a [Symfony](https://symfony.com/) ([PHP](https://php.net))/SQL backend that provides an API to the Ilios 3 Frontend that is built in javascript using [EmberJs](https://emberjs.com).  The Ilios 3 Frontend is managed by [Ilios development team](https://iliosproject.org) and the latest frontend javascript is served to users via the [Amazon Cloudfront](http://aws.amazon.com/cloudfront/) CDN Content Delivery Network).

## Frontend 
In most cases, you will never need to deplay the Ilios 3 Frontend on your own servers, as the latest version will always be up-to-date at the CDN.

## Backend
To build/deploy the Ilios 3 backend, install the default Ilios database and then follow these steps on your webserver(s) to connect to the database and serve the API.

*NOTE:* If you are updating to Ilios 3 from Ilios 2.x for the first time, please backup your database and see Updating Ilios 2.x to Ilios 3 before reading further.  If you have already updated your database schema to the one required for Ilios 3, the following steps will describe how to update the backend code.

These steps assume that you are deploying the Ilios 3 backend via a Git clone from the Ilios repository at https://github.com/ilios/ilios.git and building the install using PHP's 'composer' package manager. For installation from the Ilios 3 source distribution files, please see Installing the Ilios 3 backend from distribution file.

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
You should now be in the '/web/ilios3/ilios'  
4. Checkout the correct release version, based on its release tag ('v.3.0.0-beta1' for this example):
```bash
sudo -u apache git checkout tags/v3.0.0-beta1
```   
5. Run the following command to build the packages and its dependencies.  This step assumes you have PHP 5.4+ and Composer installed on your system:
```bash
sudo -u apache composer install
```  
This will install the required PHP Symfony packages and their dependencies.  When the process nears completion, you will be prompted with the following configuration setting options.  You should set them as noted:
```bash
database_driver: pdo_mysql
#default value, set to your value if different
 
database_host: 127.0.0.1
#Set this to your database host's IP address or hostname
 
database_port: 3306 
#3306 is the default for MySQL, but your db port may be different
 
database_name: ilios3
#enter the name of your database where your Ilios data resides
 
database_user: ilios_user
#enter the name of the user login name you use for accessing the database
 
database_password: 3x@mp73P@$$w0rd 
#this should be set to YOUR database password
 
mailer_transport: smtp
mailer_host: 127.0.0.1
mailer_user: null
mailer_password: null
#default values, set to your values if different
 
locale: en
#en = 'english', enter your desired language ISO abbreviation, if different
 
token: ThisCanBeWhateverYouLike
#this should be a long string of random characters and letters of your choosing.  If you are running in a load-balanced environment, 
#these need to be the same for all servers being deployed-to
 
debug_toolbar: false
debug_redirects: false
user_assetic_controller: false

#These should all be set to 'false' in a production environment
```   
6. Update the config.yml file in the Ilios application config folder (/web/ilios3/ilios/app/config/config.yml) to reflect the latest revision of the Ilios frontend.  For more info on this process, please see Ilios 3 Frontend Deployment  
7. Now change into the document root folder ('/web/ilios3/ilios/web') and remove the 'app_dev.php' file
```bash
cd web
sudo -u apache rm app_dev.php
```

### That's it!  You should now have an updated Ilios 3 backend!

