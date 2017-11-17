# Ilios and PHP

In an effort to ensure the best security and performance of the application overall, we have adopted a policy of requiring the latest version of PHP 7 for running Ilios.
  
### The Ilios PHP Version Policy

At any given time, the Ilios application will only be supported on the very latest minor version of PHP 7 and, for the first 3 months of that version's release, we will also ensure support for the previous minor version.  After 3 months, only Ilios instances running on the newest version of PHP 7 will continue to be supported.
 
#### Policy Example

For example, the current version of PHP is v7.1.  When PHP v7.2 is released, we will continue to ensure the Ilios code will work on PHP 7.1 for at least 90 days, and then, after that time has passed, we will only offer support for Ilios applications running on PHP 7.2 going forward.

### Currently Supported Versions of PHP

Based on the policy above, Ilios is currently compatible with the following versions of PHP:

* PHP 7.0
* PHP 7.1 (Recommended)
 
### Up-To-Date PHP Repositories for CentOS and RHEL

While some Linux distributions like Ubuntu and Fedora maintain package repositories with the very latest versions of PHP, it can be difficult to find trustworthy ones for CentOS and RHEL distributions. For RedHat-based systems such as these, we have found reliable YUM package repositories at the [IUS Community Project](https://ius.io).
