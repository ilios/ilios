# Ilios Development Instance - Quick Setup Steps for Developers and Sys Admins

If you want to work on a development instance of Ilios using a copy of your school’s own Ilios production database, or a copy of the Ilios Demo Database, here is a quick guide to the steps experienced sysadmins and developers will need to perform to get up and running in no time:

1. Have a copy of the Ilios database you would like to use, and know what Ilios backend version it is at. You can use a backup copy of your institution’s Ilios database, or you can download the latest Ilios Demo DB at [https://ilios-demo-db.iliosproject.org](https://ilios-demo-db.iliosproject.org). The version of the Ilios demo db corresponds with the latest version of Ilios at the time of download.

2. Verify the version of Ilios that you should be running with the copy of the database you plan to use. You should note this version when you create your database backup file, and you can always migrate to the latest version once it is loaded, but will want to know which version you are working with when you get started.

3. Set up a LAMP web application server (comprised of Linux, Apache, MySQL, PHP) to run the web/database services required for your development Ilios application. This is a fairly common requirement for web applications, see [here](https://github.com/ilios/ilios/blob/master/docs/install.md#pre-requisitesrequirements) for more info. RECOMMENDED: We recommend using Docker and Docker Composer! (See [below](https://github.com/ilios/ilios/edit/quickstart-docs/docs/ilios_quick_setup_for_admins.md#Using-Docker-and-Docker-Compose-to-set-up-a-Development-System-for-Ilios)!)

4. Make sure that the getcomposer.org PHP dependency manager is installed on your LAMP system.

5. Use git to checkout the correct version of the Ilios codebase to your LAMP system, or copy over the code from the Ilios instance from where you got your database backup file you plan to use for your instance. (Eg, `git checkout --tags v3.76.1`)

6. Load the database backup file into your LAMP server's database service.

7. [Re-]build the Ilios application using composer

8. Once the composer rebuild is completed, your Ilios application should now be running correctly and should be accessible at the FQDN or IP address of your LAMP system.

NOTE: If you are already using ‘form’ logins for accessing Ilios, you should be good to go, but if you are using a copy of a database and your school’s Ilios instance uses a Single Sign-On service for logins, you will need to setup or emulate the SSO setup on your development LAMP system.

## Using Docker and Docker Composer to set up a Development System for Ilios

For the fastest way to get up and running with Ilios for your development purposes, we HIGHLY recommend using Docker and Docker Composer.

### Install Docker

You will need Docker and Docker compose:

- [OS X](https://www.docker.com/docker-mac)
- [Windows](https://www.docker.com/docker-windows)
- [Ubuntu](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/)

### Running a local development server

From your ILIOS_CODE directory run:

```bash
$ composer install
$ bin/console cache:warmup
$ docker compose up -d
```

### Accessing Ilios

You should now be able to access your newly-Dockerized instance of Ilios
by visiting [http://localhost:8000](http://localhost:8000) in your browser.

### Shutting down the development server

From your ILIOS_CODE directory run:

```bash
$ docker compose down
```
