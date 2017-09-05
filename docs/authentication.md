# Authentication

## Authentication Types
Ilios supports several different authentcation types which you can use depending on your needs (form, CAS, LDAP, Shibboleth).

 **Form:** Username and password are stored in the Ilios database, users login with a simple form
 
 **CAS** Support for campus Central Authentication Service Installations, username in Ilios is linked to a campus account, users sign in through that system

**LDAP** Username and password are checked against an LDAP directory, only the username is stored in ilios

**Shibboleth** Support for campus Shibboleth Installations, username in Ilios is linked to a campus account, users sign in through that system

### Setup Ilios for your Authentication type

Change to your ilios install directory e.g. `/web/ilios3/ilios` and run the setup authentication command.
```bash
sudo -u apache bin/console ilios:setup:authentication --env=prod 
```

# Directory

If you are using CAS, LDAP, or Shibboleth authentication you will probably want to connect Ilios to your
campus directory. This allows users to be added to Ilios with all of the details they need to login. If you
do not setup a directory then you must ensure that each user has their `username` correctly populated
to match your authentication type. 

Ilios supports the **ldap** protocol for connecting to a campus directory. Unlike the **ldap** authentication
type, our directory will only use a single account to make all queries instead of users' individual accounts.

To setup the directory you must set some configuration options:

```bash
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_url <your campus value>
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_user <your campus value>
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_password <your campus value>
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_search_base <your campus value>
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_campus_id_property <your campus value>
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value ldap_directory_username_property <your campus value>
```

| Property Name| Description| Example Value|
|---|---|---|
|  ldap_directory_url  |  URL to connect to your LDAP server including protocol  |  ldaps://directory.campus.edu  |
|  ldap_directory_user  |  The user we will use to authenticate  |  uid=Ilios,ou=applications,dc=campus,dc=edu  |
|  ldap_directory_password  |  The bind password for your user  |  123GoLdap!  |
|  ldap_directory_search_base  |  What scope in the directory we should user for users  |  ou=people,dc=campus,dc=edu  |
|  ldap_directory_campus_id_property  |  In the returned data for a user what property is unique and can be used to populate the campusId field in Ilios  |  eduIDNumber  |
|  ldap_directory_username_property  |  In the returned data for a user what property contains the username that links to the **cas**, **ldap**, or **shibboleth** authentication service  |  eduPersonPrincipalName  |