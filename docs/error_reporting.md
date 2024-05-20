# Error Capturing and Reporting in Ilios

Would you like to help with Ilios development without having to do any extra work?  How about sending us your error logs in real-time so we can review them and pre-emptively fix them in the next release before they have the chance to become something larger?!

With the release of Ilios v3.57.0, you can enable the ability to automatically capture and send information about any errors or crashes of your Ilios instance to the [Ilios Team](https://www.iliosproject.org) for real-time monitoring and tracking by us.  By doing so, you can help to keep us informed of any potential bugs or problems with our code, and we can use this information to make Ilios better for everyone!

If your organization would like to share any errors or crashes in your Ilios instance with the Ilios Team, you can easily enable the error-capturing functionality required on your instance by running the console command below in the context of your web services user (eg, `apache`, `www`, `www-data`, etc.):

```bash
sudo -u apache bin/console ilios:set-config-value errorCaptureEnabled true
sudo -u apache bin/console ilios:set-config-value errorCaptureEnvironment YOUR_CAMPUS_NAME
```

This command will simply add a configuration setting named `errorCaptureEnabled` to the `application_config` table in your Ilios database and set its value to `true`.  In turn, this will activate the extra features necessary for sending information about your application's errors/issues directly to us in the background.


To disable the error-capturing feature within your Ilios app, simply run the same command, but set the configuration value to `false`, as shown:

 ```bash
 sudo -u apache bin/console ilios:set-config-value errorCaptureEnabled false
 ```
 
Only issues with your Ilios instance as tracked by its logs will be sent to us. No private information or sensitive data will be included in the information we receive, so please consider helping us create a better Ilios for everyone by enabling this extra feature in your Ilios application!

We would really appreciate it!


