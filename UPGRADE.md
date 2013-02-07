# Upgrade Instructions

## Deploy code and configuration changes

Before deploying the new codebase, it is recommended to make a complete backup of your current deployment directory.  
At a minimum, please make copies of your default landing page file and the configuration- and data-directories:

    index.php
    learning_materials
    tmp_uploads
    application/config

*Note:* Previous to Ilios v2.2, the configuration files were located in `system/application/config`.

Next, download and extract the new distribution to a separate location.

Then delete the content of your existing deployment directory and copy the content of the `web` directory of the new distro in its place.

Copy back the above listed data-directories `learning_materials` and `tmp_uploads` directories.  
Carefully review and update any `index.php` file with the `default.index.php` file in the new distribution, then copy it back into as well.

Then rename the following three configuration files in `application/config` and reconfigure them based on your previous settings. While at it, check for and configure new settings that may have been added since.

    default.config.php --> config.php
    default.database.php --> database.php
    default.ilios.php --> ilios.php

*Note:* Please do not re-deploy the previous versions of these config files _as-is_ since additional (re)configuration steps may
be necessary.

Post-deployment, please ensure that the data-, cache- and log-directories are writeable by your webserver.

    learning_materials
    tmp_uploads
    application/cache
    application/logs

## Deploy database changes

Database changes may apply, please see `database/upgrade/README.md` for instructions.
