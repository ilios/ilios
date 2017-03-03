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

### Install Docker
[Docker](https://www.docker.com/)

### Start the Ilios Demo Database
Now you can start a demo database by running the following command:

```bash
docker run -d --name ilios-demo-db -p 3306:3306 ilios/mysql-demo
```

Even after this process has exited it can take a while for the Demo database to completely load.  Take a break,
have some tea and come back to it in 10 minutes.  If you want to watch while (really boring) magic happens you can:

```bash
docker logs -f ilios-demo-db
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

If you see :

```bash
Connection refused
```

Then there is an issue with either your ilios-demo database or your parameters.yml file.

If you see :

```bash
[Database] FAIL - The database schema is not in sync with the current mapping file.
```

Then you just need to run the database migrations:
```bash
bin/console doctrine:migrations:migrate
```

### Development Server Options

##### PHP Server (Option A)
Docker does a bad job keeping local development files in sync with a container so if you are actively working and 
making many changes start a local PHP webserver  by running:

```bash
ILIOS_API_ENVIRONMENT=dev ILIOS_API_DEBUG=true bin/console server:start --router=web/app.php
```

##### Docker Server (Option B)
If you are less interested in making changes to the code and more interested in testing features
or overall performance you can build a local docker server.

- First Create the container

```bash
docker build -f Dockerfile.dev . -t ilios-web-container
```

- Second Start the server
```bash
docker run -d --name=ilios-web -p 8000:80 --link ilios-demo-db:ilios-demo-db ilios-web-container
```

- Third If you make changes to the code you must run start from scratch

```bash
docker stop ilios-web
docker rm ilios-web
docker build -f Dockerfile.dev . -t ilios-web-container
docker run --name=ilios-web -d -p 8080:80 --link ilios-demo-db:ilios-demo-db ilios-web-container
```

### Accessing Ilios

You should now be able to access your newly-Dockerized instance of Ilios 
by visiting [http://localhost:8000](http://localhost:8000) in your browser.
