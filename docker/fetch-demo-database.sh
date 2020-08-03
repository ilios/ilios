#!/bin/bash
set -euf -o pipefail

ENTRYPOINT_PATH="/docker-entrypoint-initdb.d/ilios.sql"
DOWNLOAD_PATH="/tmp/demo.sql.gz"
DATA_PATH="/tmp/demo.sql"

if [ ! -f $ENTRYPOINT_PATH ]; then
	echo 'Retrieving Ilios Demo Database...'
	/usr/bin/wget --no-verbose -O $DOWNLOAD_PATH $DEMO_DATABASE_LOCATION
	echo 'done... unpacking demo database'
	gunzip $DOWNLOAD_PATH
	echo 'done.... copying ilios demo database to by read automatically by docker'
	echo "USE ilios;" > $ENTRYPOINT_PATH
	cat $DATA_PATH >> $ENTRYPOINT_PATH
	rm $DATA_PATH
	echo 'done'
fi
