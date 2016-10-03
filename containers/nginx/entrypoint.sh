#!/bin/sh
# Note: I've written this using sh so it works in the busybox container too

/bin/mkdir -p /ilios/var/cache
/bin/mkdir -p /ilios/var/sessions
/bin/mkdir -p /ilios/var/tmp

/bin/chown -R nobody:nobody /ilios/var/cache
/bin/chown -R nobody:nobody /ilios/var/sessions
/bin/chown -R nobody:nobody /ilios/var/tmp

echo "Symfony files created and owned"

/usr/sbin/nginx