#Error Capturing and Reporting in Ilios

With the release of Ilios v3.57.0, the ability to capture and track errors and problems for real-time monitoring via the [Sentry.io](https://docs.sentry.io/) service has been added to the Ilios codebase.

If your organization uses Sentry.io, you can easily enable the error-capturing functionality required for your Ilios instance to interact with the service by running the console command below in the context of your web services user (eg, `apache`, `www`, `www-data`, etc.):

```bash
sudo -u apache bin/console ilios:set-config-value errorCaptureEnabled true
```

This command will simply add a configuration setting named `errorCaptureEnabled` to the `application_config` table in your Ilios database and set its value to true.  In turn, this will enable the activate the extra features necessary for communicating with the Sentry.io service.

To disable the error-capturing feature within your Ilios app, simply run the same command, but set the configuration value to `false`, as shown:

 ```bash
 sudo -u apache bin/console ilios:set-config-value errorCaptureEnabled false
 ```
 
 For more information on Sentry, and the services they provide, please visit their website at [https://sentry.io](https://sentry.io).
