# Interacting with Ilios from the command line

Ilios provides a host of custom commands that can be invoked from the command line.

These commands fulfill a wide range of maintenance functions, like user account management and outbound email processing.

##  Run As the Webservices User
All commands must be run on your web server as your webservices user! In these examples we will 
be using the `apache` user for this with `sudo -u apache`. You will need
to adjust this for your local environment.

## User account management

_todo_

## Outbound email processing

_todo_

## Rollover tasks

_todo_

## MeSH Import

NLM releases an updated MeSH compendium quarterly. You should update Ilios to reflect the latest MeSH changes regularly.
This process takes approximately 20 minutes. 

`sudo -u apache bin/console --env=prod ilios:maintenance:import-mesh-universe`

After this command is run you will need to clear your cache for new terms to appear in a search.

`sudo -u apache bin/console --env=prod cache:clear --no-warmup`
`sudo -u apache bin/console --env=prod cache:warmup`
 
## Developer tools 

_todo_
