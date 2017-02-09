#Ilios: Curriculum Management from UCSF

More Information at [iliosproject.org](http://iliosproject.org)

[Instructions for a new Install] (INSTALL.md)

[Updating From Ilios 2.x](UPGRADE.md)

# About

The Ilios Curriculum Management System addresses the needs of the Health Professions educational community by providing a user-friendly, flexible, and robust web application to collect, manage, analyze and deliver curricular information.

Ilios creates a comprehensive view of curricula by tracking events, learning content and activities over time. The system facilitates day-to-day administration and the delivery of information to learners; enhances curricular development, review and innovation; and reduces overhead for internal and accreditation reporting. The end result is a powerful tool creating complete and accurate pictures of complex, integrated, multi-year curricula.

Ilios manages learners and instructors and their relationships to curricular materials and activities, enabling the tracking of educational hours, roles and role transitions for participants both internal and external to an institution. Ilios also provides a robust, scalable delivery mechanism for user-centric calendar and scheduling information, with direct access to critical course information, materials, and other educational systems and services.

Ilios leverages the power of your existing online learning. With its comprehensive API, Ilios allows you to integrate with external data sources and systems. For schools using online learning systems such as Moodle or Canvas, Ilios provides a backbone of curricular information to make that deployment more robust and to complement the rich online tools, activities and materials already in use.


# Get Ilios

Download the [latest distribution](https://github.com/ilios/ilios/releases) or clone the [codebase](https://github.com/ilios/ilios).

# Contact

For more information on Ilios please contact:

The Ilios Project *
UCSF School of Medicine *
530 Parnassus Avenue *
Box 0840 *
San Francisco, CA 94143  

Email: info@iliosproject.org

# Development of Ilios

The Ilios API is a PHP application which requires a MySQL server. 
The easiest way to get a working MySQL database running is by using the 
ilios/mysql-demo docker image.

After installing [Docker](https://www.docker.com/) you can start a demo database by running the following command:

```bash
docker run -d --name db -p 3306:3306 ilios/mysql-demo
```

Then, modify your `/app/config/parameters.yml` configuration file to connect to this database:

```yaml
parameters:
    database_name: ilios
    database_driver: pdo_mysql
    database_host: 127.0.0.1
    database_port: null
    database_user: ilios
    database_password: ilios
    database_mysql_version: 5.7
```

You can test that the database is configured correctly by running:

```bash
bin/console doctrine:schema:validate
```

Start a PHP development webserver by running:

```bash
ILIOS_API_ENVIRONMENT=dev ILIOS_API_DEBUG=true bin/console server:start --router=web/app.php
```


You should now be able to access your newly-Dockerized instance of Ilios 
by visiting http://localhost:8000 in your browser.
