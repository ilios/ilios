#!/bin/bash
#
# Usage:
#   ./install_user_zero.sh db_host db_name db_username user_email_address primary_school_id
#
# (Note: You will be prompted to enter the password associated with the given db user login.)
#
#
# This script will create a user account with the given email address and linked to the given school
# in the `user` table.
# The generated user account will be assigned to the "Course Director" user role and thus grant full privileges
# to all Ilios features to its user.
#
# The generated login credentials for this account are:
#     username: 'zero_user'
#     password: 'Ch4nge_m3'
#
# ACHTUNG!
# Please make sure to change this password as soon as possible afterwards.

if [ $# -ne 5 ]
then
    echo \
"Usage: `basename $0` db_host db_name db_username user_email_address primary_school_id
  for example: `basename $0` localhost ilios_db ilios foo@bar.com 1"
    exit
fi

NOW=`date +"%F_%H.%M.%S"`;

echo "START TRANSACTION;" > _tmp_ilsql
echo "INSERT INTO user (user_id, first_name, middle_name, last_name, email, primary_school_id, added_via_ilios, enabled, examined, user_sync_ignore) VALUES (1, 'User', '$NOW', 'Zero', '$4', $5, 1, 1, 0, 0);" >> _tmp_ilsql
echo "INSERT INTO user_x_user_role (user_id, user_role_id) VALUES (1, 1);" >> _tmp_ilsql
echo "INSERT INTO authentication (person_id, password_sha256, username) VALUES (1, 'c94c0ff5358c9d2a1a7c17fc2dd46f8091c61ce0f6055066eb3b9c20f07f7ad1', 'zero_user');" >> _tmp_ilsql
echo "COMMIT;" >> _tmp_ilsql

mysql -u $3 -p -h $1 $2 < _tmp_ilsql

rm -f _tmp_ilsql
