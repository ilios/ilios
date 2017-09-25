# Ilios: Curriculum Management from UCSF

# About

The Ilios Curriculum Management System addresses the needs of the Health Professions educational community by providing a user-friendly, flexible, and robust web application to collect, manage, analyze and deliver curricular information.

Ilios creates a comprehensive view of curricula by tracking events, learning content and activities over time. The system facilitates day-to-day administration and the delivery of information to learners; enhances curricular development, review and innovation; and reduces overhead for internal and accreditation reporting. The end result is a powerful tool creating complete and accurate pictures of complex, integrated, multi-year curricula.

Ilios manages learners and instructors and their relationships to curricular materials and activities, enabling the tracking of educational hours, roles and role transitions for participants both internal and external to an institution. Ilios also provides a robust, scalable delivery mechanism for user-centric calendar and scheduling information, with direct access to critical course information, materials, and other educational systems and services.

Ilios leverages the power of your existing online learning. With its comprehensive API, Ilios allows you to integrate with external data sources and systems. For schools using online learning systems such as Moodle or Canvas, Ilios provides a backbone of curricular information to make that deployment more robust and to complement the rich online tools, activities and materials already in use.

More Information and user documentation is available at [iliosproject.org](http://iliosproject.org)

# Install and Update Instructions
[Instructions for a new Install](docs/install.md)

[Update Ilios](docs/update.md)

[Upgrading From Ilios 2.x](docs/upgrade_ilios_2_to_3.md)

## Frontend Theming

For information about customizing the look and feel of the Ilios frontend for your respective institution, please visit [docs/custom_theming.md](docs/custom_theming.md) for more information.

## API Information

If you want to connect to the Ilios API a good place to start is [docs/ilios_api.md](docs/ilios_api.md).

## Interacting with Ilios from the command line

Ilios provides a command line interface, see [docs/custom_cli_commands.md](docs/custom_cli_commands.md) 
for a list of available commands and how to use them. 

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

### Install Docker

You will need Docker and Docker compose:

- [OS X](https://www.docker.com/docker-mac)
- [Windows](https://www.docker.com/docker-windows)
- [Ubuntu](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/)


### Running a local development server

From your ILIOS_CODE directory run:

```bash
$ docker-compose pull
...
Pulling php (ilios/php-dev:latest)...
latest: Pulling from ilios/php-dev
...
```
```bash
$ docker-compose up
...
Creating network "api_default" with the default driver
Creating api_php_1 ... 
Creating api_db_1 ... 
Creating api_db_1
...
```

### Accessing Ilios

You should now be able to access your newly-Dockerized instance of Ilios 
by visiting [http://localhost:8000](http://localhost:8000) in your browser.
