/*
 * Database upgrade script for Ilios version 2.0.6b
 */

DROP TABLE IF EXISTS `ingestion_exception`;
SET character_set_client = utf8;
CREATE TABLE `ingestion_exception` (
  `user_id` INT(14) UNSIGNED NOT NULL,
  `ingested_wide_uid` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_sync_exception` (
    `exception_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `process_id` INT(10) UNSIGNED NOT NULL,
    `process_name` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
    `user_id` INT(10) UNSIGNED NOT NULL,
    `exception_code` INT(10) UNSIGNED NOT NULL COLLATE 'utf8_unicode_ci',
    `mismatched_property_name` VARCHAR(30) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
    `mismatched_property_value` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
    PRIMARY KEY (`exception_id`),
    INDEX `user_id_fkey` (`user_id`),
    CONSTRAINT `user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COMMENT='User synchronization process exceptions.'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

ALTER TABLE `user`  ADD COLUMN `user_sync_ignore` TINYINT(1) NOT NULL AFTER `examined`;
