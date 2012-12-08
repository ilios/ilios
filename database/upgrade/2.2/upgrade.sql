/*
 * Database upgrade script for Ilios version 2.2
 */

-- add indeces and foreign key restraints to the alert_instigator and alert_change tables

-- alert_instigator
ALTER TABLE  `alert_instigator` ADD INDEX (`alert_id`);
ALTER TABLE  `alert_instigator` ADD INDEX (`user_id`);
ALTER TABLE  `alert_instigator` ADD INDEX  `alert_id_user_id` (`alert_id`,`user_id`);
ALTER TABLE  `alert_instigator` ADD CONSTRAINT FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE;
ALTER TABLE  `alert_instigator` ADD CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

-- alert_change
ALTER TABLE  `alert_change` ADD INDEX (`alert_id`);
ALTER TABLE  `alert_change` ADD INDEX `alert_id_alert_change_type_id` (`alert_id`,`alert_change_type_id`);
ALTER TABLE  `alert_change` ADD CONSTRAINT FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE;
