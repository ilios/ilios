# Interacting with Ilios from the command line

Ilios provides a host of custom commands that can be invoked from the command line.

These commands fulfill a wide range of maintenance functions, like user account management and outbound email processing.

## Run As the Webservices User

All commands must be run on your web server as your webservices user! In these examples we will be using the `apache` user for this with `sudo -u apache`. You will need to adjust this for your local environment.

## User account management

Create a user, find an existing one, or change an existing one's password.

`sudo -u apache bin/console --env=prod ilios:add-user`
`sudo -u apache bin/console --env=prod ilios:find-user <searchTerm>`
`sudo -u apache bin/console --env=prod ilios:change-password <userId>`

## Outbound email processing

Send out change alert message to configured email recipients.

`sudo -u apache bin/console --env=prod ilios:send-change-alerts`

Send out a test email, to ensure the system is working.

`sudo -u apache bin/console --env=prod ilios:send-test-email`

## Rollover tasks

Rolls over (copies) a given curriculum inventory report.

`sudo -u apache bin/console --env=prod ilios:rollover-ci-report <reportId>`

Roll over a course to a new year using its `course_id`.

`sudo -u apache bin/console --env=prod ilios:rollover-course <courseId> <newAcademicYear>`

## MeSH Import

NLM releases an updated MeSH compendium quarterly. You should update Ilios to reflect the latest MeSH changes regularly. This process takes approximately 20 minutes.

`sudo -u apache bin/console --env=prod ilios:maintenance:import-mesh-universe`

After this command is run you will need to clear your cache for new terms to appear in a search.

`sudo -u apache bin/console --env=prod cache:clear --no-warmup`
`sudo -u apache bin/console --env=prod cache:warmup`

## Developer tools

Set a configuration value in the database.

`sudo -u apache bin/console --env=prod ilios:set-config-value [-r|--remove] [--] <name> [<value>]`
