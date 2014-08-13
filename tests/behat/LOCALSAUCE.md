How-to run tests against any browser using SauceLabs
====================================================

The assumption is that you are doing this on a recent Fedora/RHEL (or derivative) Linux distro. These instructions also seem to work for Mac OS X.

Prerequisites
-------------

0. **DO NOT RUN THESE TESTS AGAINST A PRODUCTION INSTANCE**. These tests will pollute your database.

1. Add the following line to your `/etc/hosts` file to ensure you can access your Ilios install at "https://localilios". Using `localhost` will not work due to SauceLabs setup.

    127.0.0.1 localilios

2. Install Composer. See http://getcomposer.org/doc/00-intro.md for instructions.

3. Install Behat and co. via Composer.

    cd <iliosroot>/tests/behat
    composer install

Run tests
---------
You now need to setup your Sauce username and accesskey.  These can be found at https://saucelabs.com/account:

    export SAUCE_USERNAME=<username>
    export SAUCE_ACCESS_KEY=<accesskey>

It is now possible to use Sauce to test your local Ilios environment in many different browsers.  A listing of available setups can be found in `saucelabs.yml`.
here are some examples:

1. Windows 7 IE9

    cd <iliosroot>/tests/behat
    bin/behat -c saucelabs.yml -p windows7ie9

2. Mac OSX Safari

    cd <iliosroot>/tests/behat
    bin/behat -c saucelabs.yml -p osxsafari

3. Windows 7 Firefox

    cd <iliosroot>/tests/behat
    bin/behat -c saucelabs.yml -p windows7firefox
