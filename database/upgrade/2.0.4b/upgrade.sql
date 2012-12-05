/*
 * Database upgrade script for Ilios v2.0.4b
 */

ALTER TABLE user DROP COLUMN `primary_user_role_id`;
ALTER TABLE user DROP COLUMN `galen_id`;
