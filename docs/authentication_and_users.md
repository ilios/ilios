# Authentication and Users

Ilios provides several methods for populating and authenticating users within the application, natively and via external processes or systems.

## Authentication Types

Ilios supports several different authentcation types to choose from, depending on your needs: Form, LDAP, CAS, Shibboleth.

**Form:** Username and password are stored in the Ilios database, users login with a simple form - this is the default authentication type for a new Ilios installation.

**LDAP** Username and password are authenticated against an LDAP directory connection, only the username is stored in ilios

**CAS** Support for campus Central Authentication Service Installations, username in Ilios is linked to a campus account, users sign in through that system (supports SAML)

**Shibboleth** Support for campus SAML Shibboleth installations, username in Ilios is linked to a campus account, users sign in through that system. For information on the specifics of configuring Ilios to use Shibboleth, please see our docs on [Shibboleth Authentication](shibboleth_authenticaion.md) specifically.

### Initial Setup Ilios for your Authentication type

Change to your Ilios application directory (e.g. `/var/www/ilios`) and run the setup authentication command.

```bash
sudo -u apache bin/console ilios:setup:authentication --env=prod
```

## Using an LDAP Directory Connection for User Population

If you are using on of the CAS, LDAP, or Shibboleth authentication types, you will probably want to also connect Ilios to your campus directory in order for users to be programmatically added to Ilios with all of the details they will need to successfully login. If you do not connect Ilios to your campus directory, you will need to ensure that each user has their respective `username` correctly entered into the Ilios application to exactly match the necessary attribute value(s) of your chosen authentication type.

Ilios supports the **ldap** protocol for connecting to a campus directory. Unlike the **ldap** authentication
type, our directory will only use a single account to make all queries instead of users' individual accounts. This is typically a 'service' account created by LDAP administrators expressly for use by Ilios to make directory lookups.

To setup connectivity with an LDAP directory you must set some configuration options:

```bash
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_url <your campus value>

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_user <your campus value>

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_password <your campus value>

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_search_base <your campus value>

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_campus_id_property <your campus value>

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_username_property <your campus value>
```

| Property Name                                | Description                                                                                                                                       | Example Value                              |
|----------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------|
| ldap_directory_url                           | URL to connect to your LDAP server including protocol                                                                                             | ldaps://directory.campus.edu               |
| ldap_directory_user                          | The user we will use to authenticate                                                                                                              | uid=Ilios,ou=applications,dc=campus,dc=edu |
| ldap_directory_password                      | The bind password for your user                                                                                                                   | 123GoLdap!                                 |
| ldap_directory_search_base                   | What scope in the directory we should user for users                                                                                              | ou=people,dc=campus,dc=edu                 |
| ldap_directory_campus_id_property            | In the returned data for a user what property is unique and can be used to populate the campusId field in Ilios                                   | eduIDNumber                                |
| ldap_directory_username_property             | In the returned data for a user what property contains the username that links to the **cas**, **ldap**, or **shibboleth** authentication service | eduPersonPrincipalName                     |
| ldap_directory_first_name_property           | In the returned data for a user what property contains the first name. If this isn't provided it will default to **givenName**                    | givenName                                  |
| ldap_directory_last_name_property            | In the returned data for a user what property contains the last name. If this isn't provided it will default to **sn**                            | sn                                         |
| ldap_directory_preferred_first_name_property | In the returned data for a user what property contains the users preferred first name                                                             | preferredFirstName                         |
| ldap_directory_preferred_last_name_property  | In the returned data for a user what property contains the users preferred last name                                                              | preferredLastName                          |
