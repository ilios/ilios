# Shibboleth Authentication

Ilios includes support for institutions using [Shibboleth](https://www.internet2.edu/products-services/trust-identity/shibboleth/) for their SAML-based Single Sign-On authenticaton solution.

If you would like to enable Shibboleth authentication in Ilios, you should first follow the steps provided by your institution for setting up your Ilios host system as a Shibboleth Service Provider, or "SP". When You have completed the steps they outline as required, you will need to make several configuration changes to your webserver (Apache) configuration, as well as within the Ilios configuratrion settings in order to activate it.

## Web Server Settings

_Note: Currently, Ilios only provides documentation for webservers running the Apache 2.4 httpd web server software. For Nginx or other servers, the settings may be different from what is included here._

When you install the Shibboleth software package on your application server, a Shibboleth configuration file for Apache should be created for you automatically as part of the package install. On Redhat-based Linux installations, this file is typically named `shibd.conf` and resides in your Apache extension configuration directory (typically `/etc/httpd/conf.d/` or `/etc/apache2/conf.d/`).

Most of the default settings of this file can remain unchanged, but it is important to update the settings within the `<Location></Location>` configuration container with the following settings to ensure that the entire Ilios application is protected, as well as simplifying the Shibboleth authentication 'handshake' process to accommodate Ilios' use of JSON Web Tokens for authentication/permissions-checking.

For an Ilios instance, the `<Location></Location>` container in the `shibd.conf` should read as follows:

```conf
<Location />
  AuthType shibboleth
  Require shibboleth
</Location>
```

## Ilios Configuration Settings

After configuring your Ilios application server as a Shibboleth SP and verifying its functionality with your institution's Shibboleth Identity Provider (or "IDp"), you will need to set `shibboleth` as the authentication method within the Ilios configuration settings:

First, make sure that you set any `ILIOS_AUTHENTICATION_TYPE` environment variables on the system to `shibboleth`. Environment variables in Ilios can be set in several places, but are typically found in the `.env.local` dotfile in your Ilios application root. Please see our [documentation on ENV vars and Configuration](env_vars_and_config.md) for more information on where environment variables can be set for an Ilios installation.

Second, you will need to set/update the following settings to reflect their proper values in the `application_config` table of the Ilios application's database:

|`name`|`value`||
|---|---|---|
|`authentication_type`|`shibboleth`||
|`shibboleth_authentication_login_path`|`/Shibboleth.sso/Login`| (<-- Default Shibboleth value, your institution's setting may differ)|
|`shibboleth_authentication_logout_path`|`/Shibboleth.sso/Logout`| (<-- Default Shibboleth value, your institution's setting may differ)|

If necessary, these settings can be easily added or updated using the following console commands from within the Ilios application directory, run in the context of your Web-service user (eg, `apache`):

```bash
sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value authentication_type shibboleth

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value shibboleth_authentication_login_path <your campus value>

sudo -u apache bin/console --env=prod ilios:maintenance:set-config-value shibboleth_authentication_logout_path <your campus value>
```

Finally, you should rebuild your Ilios instance after making these changes by re-running the `composer install` process as outlined in the Ilios installation documentation at [install.md](install.md).
