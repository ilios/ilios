/*
 * Database upgrade script for Ilios version 2.2
 */

-- add indeces and foreign key restraints to the alert_instigator and alert_change tables

-- alert_instigator

-- First, let's delete all the orphan records...
DELETE FROM `alert_instigator` WHERE `user_id` NOT IN (SELECT `user_id` FROM `user`);
DELETE FROM `alert_instigator` WHERE `alert_id` NOT IN (SELECT `alert_id` FROM `alert`);

-- then, let's add the indexes and foreign key constraints...
ALTER TABLE  `alert_instigator` ADD INDEX (`alert_id`);
ALTER TABLE  `alert_instigator` ADD INDEX (`user_id`);
ALTER TABLE  `alert_instigator` ADD INDEX  `alert_id_user_id` (`alert_id`,`user_id`);
ALTER TABLE  `alert_instigator` ADD CONSTRAINT `fkey_alert_instigator_alert_id` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE;
ALTER TABLE  `alert_instigator` ADD CONSTRAINT `fkey_alert_instigator_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

-- alert_change

-- delete the orphan records...
DELETE FROM `alert_change` WHERE `alert_id` NOT IN (SELECT `alert_id` FROM `alert`);

-- add the indexes and FKC's...
ALTER TABLE  `alert_change` ADD INDEX (`alert_id`);
ALTER TABLE  `alert_change` ADD INDEX `alert_id_alert_change_type_id` (`alert_id`,`alert_change_type_id`);
ALTER TABLE  `alert_change` ADD CONSTRAINT `fkey_alert_change_alert_id` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE;

-- add [composite] primary key and foreign key constraints to user_x_user_role table

-- user_x_user_role

-- delete the orphan records...
DELETE FROM `user_x_user_role` WHERE `user_id` NOT IN (SELECT `user_id` FROM `user`);
DELETE FROM `user_x_user_role` WHERE `user_role_id` NOT IN (SELECT `user_role_id` FROM `user_role`);

-- add the indexes and FKC's...
ALTER TABLE  `user_x_user_role` ADD PRIMARY KEY (`user_id`,`user_role_id`) USING BTREE;
ALTER TABLE  `user_x_user_role` ADD INDEX `user_x_user_role_user_id` (`user_id`);
ALTER TABLE  `user_x_user_role` ADD INDEX `user_x_user_role_user_role_id` (`user_role_id`);
ALTER TABLE  `user_x_user_role` ADD CONSTRAINT `fkey_user_x_user_role_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE  `user_x_user_role` ADD CONSTRAINT `fkey_user_x_user_role_user_role_id` FOREIGN KEY (`user_role_id`) REFERENCES `user_role` (`user_role_id`) ON DELETE CASCADE ON UPDATE RESTRICT;