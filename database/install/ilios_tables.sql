
--
--
-- This creates a fresh table structure for the Ilios application; this does not populate any of the
-- newly created tables with actual data.
--
--
--
--
--




--
--
--
--
-- Primary entity tables
--
--
--
--

	--
	-- Table school
	--

CREATE TABLE `school` (
  `school_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_prefix` VARCHAR(8) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
  `title` VARCHAR(60) NOT NULL COLLATE 'utf8_unicode_ci',
  `ilios_administrator_email` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
  `deleted` TINYINT(1) NOT NULL,
  `change_alert_recipients` TEXT NULL COLLATE 'utf8_unicode_ci',
  PRIMARY KEY (`school_id`),
  UNIQUE INDEX `template_prefix` (`template_prefix`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	--
	-- Table user
	--

	DROP TABLE IF EXISTS `user`;
	SET character_set_client = utf8;
	CREATE TABLE `user` (
	  `user_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `last_name` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL,
	  `first_name` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
	  `middle_name` VARCHAR(20) COLLATE utf8_unicode_ci,
	  `phone` VARCHAR(30) COLLATE utf8_unicode_ci,
	  `email` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
	  `primary_school_id` INT(10) UNSIGNED NOT NULL,
	  `added_via_ilios` TINYINT(1) NOT NULL,
	  `enabled` TINYINT(1) NOT NULL,			-- if an account is disabled, the user cannot log in
	  `uc_uid` VARCHAR(16) COLLATE utf8_unicode_ci,		-- visitors and volunteer faculty (at least) will not have; need uniquer
	  `other_id` VARCHAR(16) COLLATE utf8_unicode_ci,
	  `examined` TINYINT(1) NOT NULL,			-- at the beginning of an EDS sync, we clear this, then set it if found in the EDS return
	  `user_sync_ignore` TINYINT(1) NOT NULL,
	  PRIMARY KEY (`user_id`) USING BTREE,
    INDEX `fkey_user_primary_school` (`primary_school_id`),
    CONSTRAINT `fkey_user_primary_school` FOREIGN KEY (`primary_school_id`) REFERENCES `school` (`school_id`) ON UPDATE RESTRICT ON DELETE RESTRICT
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	--
	-- Table authentication
	--
    DROP TABLE IF EXISTS `authentication`;
    SET character_set_client = utf8;
    CREATE TABLE `authentication` (
      `username` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
      `password_sha256` VARCHAR(64) COLLATE 'utf8_unicode_ci' NOT NULL,
      `person_id` INT(14) UNSIGNED NOT NULL,
      `api_key` VARCHAR(64) COLLATE 'utf8_unicode_ci' DEFAULT NULL,
    PRIMARY KEY (`person_id`) USING BTREE,
    UNIQUE INDEX `username` (`username`),
    UNIQUE INDEX `api_key` (`api_key`),
    CONSTRAINT `fkey_authentication_user` FOREIGN KEY (`person_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	--
	-- Table user_role
	--

	DROP TABLE IF EXISTS `user_role`;
	SET character_set_client = utf8;
	CREATE TABLE `user_role` (
	  `user_role_id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`user_role_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
	-- Table program
	--

DROP TABLE IF EXISTS `program`;
SET character_set_client = utf8;
CREATE TABLE `program` (
    `program_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL,
    `short_title` VARCHAR(10) COLLATE utf8_unicode_ci NOT NULL,
    `publish_event_id` INT(14) UNSIGNED,		-- if null, the row is still in draft mode
    `duration` TINYINT(1) UNSIGNED NOT NULL,
    `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
    `owning_school_id` INT(10) UNSIGNED NOT NULL,
    `published_as_tbd` TINYINT(1) NOT NULL,	-- this value is ignored if publish_event_id is NULL
    PRIMARY KEY (`program_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
	-- Table program_year
	--

DROP TABLE IF EXISTS `program_year`;
SET character_set_client = utf8;
CREATE TABLE `program_year` (
    `program_year_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `publish_event_id` INT(14) UNSIGNED,			-- if null, the row is still in draft mode
    `start_year` SMALLINT(4) UNSIGNED NOT NULL,
    `program_id` INT(14) UNSIGNED NOT NULL,
    `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
    `locked` TINYINT(1) NOT NULL,			-- marked as locked prevents it from being modified
    `archived` TINYINT(1) NOT NULL,		-- marked as archived prevents it from being found in searches - but is different semantically from 'deleted'
    `published_as_tbd` TINYINT(1) NOT NULL,	-- this value is ignored if publish_event_id is NULL
    PRIMARY KEY (`program_year_id`) USING BTREE,
    CONSTRAINT `fkey_program_year_program_id` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
	-- Table cohort
	--

DROP TABLE IF EXISTS `cohort`;
SET character_set_client = utf8;
CREATE TABLE `cohort` (
    `cohort_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
    `program_year_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`cohort_id`) USING BTREE,
    KEY `whole_k` USING BTREE (`program_year_id`,`cohort_id`,`title`),
    CONSTRAINT `fkey_cohort_program_year_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	--
	-- Table group
	--

	DROP TABLE IF EXISTS `group`;
	SET character_set_client = utf8;
	CREATE TABLE `group` (
	  `group_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  `instructors` VARCHAR(120) COLLATE utf8_unicode_ci,
	  `location` VARCHAR(100) COLLATE utf8_unicode_ci,
	  `parent_group_id` INT(14) UNSIGNED,
	  `cohort_id` INT(14) UNSIGNED NOT NULL,
    CONSTRAINT `fkey_group_cohort_id` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON DELETE CASCADE,
	  PRIMARY KEY (`group_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table instructor_group
	--		since this is a simple set of users (instructors), let's try breaking
	--				it out as a separate entity from the group table
	--

	DROP TABLE IF EXISTS `instructor_group`;
	SET character_set_client = utf8;
	CREATE TABLE `instructor_group` (
	  `instructor_group_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  `school_id` INT(10) UNSIGNED NOT NULL,
	  PRIMARY KEY (`instructor_group_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table instruction_hours
	--

	DROP TABLE IF EXISTS `instruction_hours`;
	SET character_set_client = utf8;
	CREATE TABLE `instruction_hours` (
	  `instruction_hours_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `generation_time_stamp` TIMESTAMP NOT NULL,				-- when this calculation was performed
	  `hours_accrued` INT(14) UNSIGNED NOT NULL,
	  `modified` TINYINT(1) NOT NULL,					-- if true, future automatic calculations for this user-session pair will not touch this row
	  `modification_time_stamp` TIMESTAMP,					-- non-NULL if modified == true
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  `session_id` INT(14) UNSIGNED NOT NULL,
	  PRIMARY KEY (`instruction_hours_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	--
	-- Table department
	--

	DROP TABLE IF EXISTS `department`;
	SET character_set_client = utf8;
	CREATE TABLE `department` (
	  `department_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(90) COLLATE utf8_unicode_ci NOT NULL,
	  `school_id` INT(10) UNSIGNED NOT NULL,		-- this is the owning school
	  `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
	  PRIMARY KEY (`department_id`) USING BTREE,
	  KEY `department_school_k` USING BTREE (`department_id`,`school_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;





	--
	-- Table course_clerkship_type
	--

	DROP TABLE IF EXISTS `course_clerkship_type`;
	CREATE TABLE `course_clerkship_type` (
		`course_clerkship_type_id` INT(10) NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(20) NOT NULL,
		PRIMARY KEY (`course_clerkship_type_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	--
	-- Table course
	--

	DROP TABLE IF EXISTS `course`;
	SET character_set_client = utf8;
	CREATE TABLE `course` (
	  `course_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL,
	  `publish_event_id` INT(14) UNSIGNED,		-- if null, the row is still in draft mode
	  `course_level` SMALLINT(2) UNSIGNED NOT NULL,
	  `year` SMALLINT(4) UNSIGNED NOT NULL,
	  `start_date` DATE NOT NULL,
	  `end_date` DATE NOT NULL,
	  `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
	  `external_id` VARCHAR(18) COLLATE utf8_unicode_ci,
	  `locked` TINYINT(1) NOT NULL,			-- marked as locked prevents it (and its children (sessions, offerings, ...) from being modified
	  `archived` TINYINT(1) NOT NULL,		-- marked as archived prevents it (and its children (sessions, offerings, ...) from being found in searches - but is different semantically from 'deleted'
	  `owning_school_id` INT(10) UNSIGNED NOT NULL,
	  `published_as_tbd` TINYINT(1) NOT NULL,	-- this value is ignored if publish_event_id is NULL
	  `clerkship_type_id` INT(10) NULL DEFAULT NULL,
	  PRIMARY KEY (`course_id`) USING BTREE,
	  KEY `title_course_k` USING BTREE (`course_id`,`title`),
	  KEY `external_id_k` USING BTREE (`external_id`),
	  INDEX `clerkship_type_id` (`clerkship_type_id`),
	  CONSTRAINT `clerkship_type_id` FOREIGN KEY (`clerkship_type_id`) REFERENCES `course_clerkship_type` (`course_clerkship_type_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table assessment_option
--
DROP TABLE IF EXISTS `assessment_option`;
CREATE TABLE `assessment_option` (
    `assessment_option_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`assessment_option_id`),
    UNIQUE INDEX `name` (`name`)
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

--
-- Table session_type
--

DROP TABLE IF EXISTS `session_type`;
SET character_set_client = utf8;
CREATE TABLE `session_type` (
    `session_type_id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    `owning_school_id` INT(10) unsigned NOT NULL,
    `session_type_css_class` VARCHAR(64) NULL,
    `assessment` BOOL NOT NULL DEFAULT 0,
    `assessment_option_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (`session_type_id`) USING BTREE,
    FOREIGN KEY (`owning_school_id`) REFERENCES school(school_id),
    INDEX `assessment_option_fkey` (`assessment_option_id`),
    CONSTRAINT `assessment_option_fkey`
        FOREIGN KEY (`assessment_option_id`) REFERENCES `assessment_option` (`assessment_option_id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

	--
	-- Table ilm_session_facet
	--

	DROP TABLE IF EXISTS `ilm_session_facet`;
	SET character_set_client = utf8;
	CREATE TABLE `ilm_session_facet` (
	  `ilm_session_facet_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `hours` DECIMAL(6,2) UNSIGNED NOT NULL,
	  `due_date` DATE NOT NULL,
	  PRIMARY KEY (`ilm_session_facet_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



	--
	-- Table session
	--

	DROP TABLE IF EXISTS `session`;
	SET character_set_client = utf8;
	CREATE TABLE `session` (
	  `session_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL,
	  `publish_event_id` INT(14) UNSIGNED,		-- if null, the row is still in draft mode
	  `attire_required` TINYINT(1) NOT NULL,
	  `equipment_required` TINYINT(1) NOT NULL,
	  `supplemental` TINYINT(1) NOT NULL,
	  `course_id` INT(14) UNSIGNED NOT NULL,
	  `session_type_id` INT(3) UNSIGNED NOT NULL,
	  `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
	  `ilm_session_facet_id` INT(14) UNSIGNED,	-- if this is non-null, there should be no offerings associated to this session (and ignored if existing)
	  `published_as_tbd` TINYINT(1) NOT NULL,	-- this value is ignored if publish_event_id is NULL
	  `last_updated_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`session_id`) USING BTREE,
	  KEY `session_type_id_k` USING BTREE (`session_type_id`),
	  KEY `course_id_k` USING BTREE (`course_id`),
	  KEY `session_course_type_title_k` USING BTREE (`session_id`,`course_id`,`session_type_id`,`title`),
	  CONSTRAINT `session_ibfk_1` FOREIGN KEY (`session_type_id`) REFERENCES `session_type` (`session_type_id`),
	  CONSTRAINT `session_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
	  CONSTRAINT `session_ibfk_3` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




	--
	-- Table session_description
	--

	DROP TABLE IF EXISTS `session_description`;
	SET character_set_client = utf8;
	CREATE TABLE `session_description` (
	  `session_id` INT(14) UNSIGNED NOT NULL,
	  `description` TEXT COLLATE utf8_unicode_ci NOT NULL,
	  KEY `session_id_k` USING BTREE (`session_id`),
	  CONSTRAINT `session_description_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table offering
	--

	DROP TABLE IF EXISTS `offering`;
	SET character_set_client = utf8;
	CREATE TABLE `offering` (
	  `offering_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `room` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  `publish_event_id` INT(14) UNSIGNED,		-- if null, the row is still in draft mode
	  `session_id` INT(14) UNSIGNED NOT NULL,
	  `start_date` DATETIME NOT NULL,		-- the code assumes this is stored in UTC
	  `end_date` DATETIME NOT NULL,			-- the code assumes this is stored in UTC
	  `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
	  `last_updated_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`offering_id`) USING BTREE,
	  KEY `session_id_k` USING BTREE (`session_id`),
	  KEY `offering_dates_session_k` USING BTREE (`offering_id`,`session_id`,`start_date`,`end_date`),
	  CONSTRAINT `offering_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table recurring_event
	--

	DROP TABLE IF EXISTS `recurring_event`;
	SET character_set_client = utf8;
	CREATE TABLE `recurring_event` (
	  `recurring_event_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `on_sunday` TINYINT(1) UNSIGNED NOT NULL,
	  `on_monday` TINYINT(1) UNSIGNED NOT NULL,
	  `on_tuesday` TINYINT(1) UNSIGNED NOT NULL,
	  `on_wednesday` TINYINT(1) UNSIGNED NOT NULL,
	  `on_thursday` TINYINT(1) UNSIGNED NOT NULL,
	  `on_friday` TINYINT(1) UNSIGNED NOT NULL,
	  `on_saturday` TINYINT(1) UNSIGNED NOT NULL,
	  `end_date` DATETIME NOT NULL,			-- the code assumes this is stored in UTC
	  `repetition_count` TINYINT(1) UNSIGNED,
	  `previous_recurring_event_id` INT(14) UNSIGNED,
	  `next_recurring_event_id` INT(14) UNSIGNED,
	  PRIMARY KEY (`recurring_event_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table discipline
	--

	DROP TABLE IF EXISTS `discipline`;
	SET character_set_client = utf8;
	CREATE TABLE `discipline` (
	  `discipline_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL,
	  `owning_school_id` INT(10) UNSIGNED NOT NULL,
	  PRIMARY KEY (`discipline_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table competency
	--

	DROP TABLE IF EXISTS `competency`;
	SET character_set_client = utf8;
	CREATE TABLE `competency` (
          `competency_id` int(14) unsigned NOT NULL auto_increment,
          `title` varchar(200) collate utf8_unicode_ci NOT NULL,
          `parent_competency_id` int(14) unsigned default NULL,
	  `owning_school_id` INT(10) UNSIGNED NOT NULL,
          PRIMARY KEY  (`competency_id`),
          KEY `parent_competency_id_k` (`parent_competency_id`),
          CONSTRAINT `competency_ibfk_1` FOREIGN KEY (`parent_competency_id`) REFERENCES `competency` (`competency_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Table objective
--
DROP TABLE IF EXISTS `objective`;
CREATE TABLE `objective` (
    `objective_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` TEXT COLLATE utf8_unicode_ci NOT NULL,
    `competency_id` int(14) unsigned default NULL,
    PRIMARY KEY (`objective_id`) USING BTREE,
    INDEX `fkey_objective_competency` (`competency_id`),
    CONSTRAINT `fkey_objective_competency`
        FOREIGN KEY (`competency_id`)
        REFERENCES `competency` (`competency_id`)
        ON UPDATE RESTRICT ON DELETE RESTRICT
)
AUTO_INCREMENT=1
DEFAULT CHARSET=utf8
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;



	--
	-- Table learning_material
	--

	DROP TABLE IF EXISTS `learning_material`;
	SET character_set_client = utf8;
	CREATE TABLE `learning_material` (
	  `learning_material_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
  	  `mime_type` VARCHAR(96) COLLATE utf8_unicode_ci NOT NULL,
	  `relative_file_system_location` VARCHAR(128) COLLATE utf8_unicode_ci,			-- this is relative to the storage directory
	  `filename` VARCHAR(255) COLLATE utf8_unicode_ci,
  	  `filesize` INT(12) UNSIGNED NOT NULL,
	  `description` TEXT COLLATE utf8_unicode_ci NOT NULL,
	  `copyright_ownership` TINYINT(1) UNSIGNED NOT NULL,					-- 0==don't have ownership; 1==do; 2==NA
	  `copyright_rationale` TEXT COLLATE utf8_unicode_ci,
	  `upload_date` DATETIME NOT NULL,							-- the code assumes this is stored in UTC
	  `owning_user_id` INT(14) UNSIGNED NOT NULL,
	  `asset_creator` VARCHAR(80) COLLATE utf8_unicode_ci,
	  `web_link` VARCHAR(256) COLLATE utf8_unicode_ci,
	  `citation` VARCHAR(512) COLLATE utf8_unicode_ci,
	  `learning_material_status_id` INT(2) UNSIGNED NOT NULL,
	  `learning_material_user_role_id` INT(2) UNSIGNED NOT NULL,
	  PRIMARY KEY (`learning_material_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table learning_material_status
	--

	DROP TABLE IF EXISTS `learning_material_status`;
	SET character_set_client = utf8;
	CREATE TABLE `learning_material_status` (
	  `learning_material_status_id` INT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`learning_material_status_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table learning_material_user_role
	--

	DROP TABLE IF EXISTS `learning_material_user_role`;
	SET character_set_client = utf8;
	CREATE TABLE `learning_material_user_role` (
	  `learning_material_user_role_id` INT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`learning_material_user_role_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table user_made_reminder
	--

	DROP TABLE IF EXISTS `user_made_reminder`;
	SET character_set_client = utf8;
	CREATE TABLE `user_made_reminder` (
	  `user_made_reminder_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `note` VARCHAR(150) COLLATE utf8_unicode_ci NOT NULL,
	  `creation_date` DATETIME NOT NULL,		-- the code assumes this is stored in UTC
	  `due_date` DATETIME NOT NULL,			-- the code assumes this is stored in UTC
	  `closed` TINYINT(1) NOT NULL,
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  PRIMARY KEY (`user_made_reminder_id`) USING BTREE,
	  KEY `due_closed_user_k` USING BTREE (`due_date`, `closed`, `user_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table ingestion_exception
	--

	DROP TABLE IF EXISTS `ingestion_exception`;
	SET character_set_client = utf8;
	CREATE TABLE `ingestion_exception` (
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  `ingested_wide_uid` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`user_id`) USING BTREE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table report
	--

	DROP TABLE IF EXISTS `report`;
	SET character_set_client = utf8;
	CREATE TABLE `report` (
	  `report_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(240) COLLATE utf8_unicode_ci,
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  `creation_date` DATETIME NOT NULL,		-- the code assumes this is stored in UTC
	  `subject` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL,
	  `prepositional_object` VARCHAR(32) COLLATE utf8_unicode_ci,	-- if non-null, there must be 1-N report_po_value association to this report_id
	  `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
	  PRIMARY KEY (`report_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	--
	-- Table report_po_value
	--

	DROP TABLE IF EXISTS `report_po_value`;
	SET character_set_client = utf8;
	CREATE TABLE `report_po_value` (
	  `report_id` INT(14) UNSIGNED NOT NULL,
	  `prepositional_object_table_row_id` VARCHAR(14) NOT NULL,		-- must be varchar for mesh descriptor uids.. penser
	  `deleted` TINYINT(1) NOT NULL,		-- nothing is ever 'deleted', but marked as deleted prevents it from being found in searches
	  CONSTRAINT `fkey_report_po_value_report_id` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table alert
	--

	DROP TABLE IF EXISTS `alert`;
	SET character_set_client = utf8;
	CREATE TABLE `alert` (
	  `alert_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `table_row_id` INT(14) UNSIGNED NOT NULL,
	  `table_name` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL,
	  `additional_text` TEXT COLLATE utf8_unicode_ci,
	  `dispatched` TINYINT(1) NOT NULL,
	  PRIMARY KEY (`alert_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table alert_change_type
	--

	DROP TABLE IF EXISTS `alert_change_type`;
	SET character_set_client = utf8;
	CREATE TABLE `alert_change_type` (
	  `alert_change_type_id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `title` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`alert_change_type_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table audit_event
	--

	DROP TABLE IF EXISTS `audit_event`;
	SET character_set_client = utf8;
	CREATE TABLE `audit_event` (
	  `audit_event_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `time_stamp` TIMESTAMP NOT NULL,
	  `user_id` INT(14) UNSIGNED,
	  PRIMARY KEY (`audit_event_id`) USING BTREE,
	  KEY `user_id_k` USING BTREE (`user_id`),
	  KEY `ae_u_ts_k` USING BTREE (`audit_event_id`,`user_id`,`time_stamp`),
	  CONSTRAINT `audit_event_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	--
	-- Table audit_atom
	--

	DROP TABLE IF EXISTS `audit_atom`;
	SET character_set_client = utf8;
	CREATE TABLE `audit_atom` (
	  `audit_atom_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `table_row_id` INT(14) UNSIGNED NOT NULL,
	  `table_column` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
	  `table_name` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
	  `event_type` TINYINT(1) UNSIGNED NOT NULL,	-- Cr, U, D
	  `root_atom` TINYINT(1) NOT NULL,		-- nearly every audit event should have one root atom, the 'cause' of the audit event (multi-save-offerings will not)
	  `audit_event_id` INT(14) UNSIGNED NOT NULL,
	  PRIMARY KEY (`audit_atom_id`) USING BTREE,
	  KEY `audit_event_id_k` USING BTREE (`audit_event_id`),
	  KEY `aeid_ra_k` USING BTREE (`audit_event_id`,`root_atom`),
	  CONSTRAINT `audit_atom_ibfk_1` FOREIGN KEY (`audit_event_id`) REFERENCES `audit_event` (`audit_event_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	--
	-- Table audit_content
	--

	DROP TABLE IF EXISTS `audit_content`;
	SET character_set_client = utf8;
	CREATE TABLE `audit_content` (
	  `audit_atom_id` INT(14) UNSIGNED NOT NULL,
	  `serialized_state_event` MEDIUMBLOB NOT NULL,
	  KEY `audit_atom_id_k` USING BTREE (`audit_atom_id`),
	  CONSTRAINT `audit_content_ibfk_1` FOREIGN KEY (`audit_atom_id`) REFERENCES `audit_atom` (`audit_atom_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table publish_event
	--
	--	any given publishable row could be published multiple times
	--

	DROP TABLE IF EXISTS `publish_event`;
	SET character_set_client = utf8;
	CREATE TABLE `publish_event` (
	  `publish_event_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `administrator_id` INT(14) UNSIGNED NOT NULL,
	  `machine_ip` VARCHAR(15) COLLATE utf8_unicode_ci NOT NULL,	-- the ip of the machine the admin was logged in from when making the change
	  `time_stamp` TIMESTAMP NOT NULL,
	  `table_name` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL,
	  `table_row_id` INT(14) UNSIGNED NOT NULL,			-- the primary key row id for the publish event
	  PRIMARY KEY (`publish_event_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



	--
	-- Table database_metadata
	--
	--	the row with the highest id will represent the current state of the db
	--

	DROP TABLE IF EXISTS `database_metadata`;
	SET character_set_client = utf8;
	CREATE TABLE `database_metadata` (
	  `database_metadata_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `time_stamp` TIMESTAMP NOT NULL,
	  `mesh_release_version` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
	  `last_som_feed` TIMESTAMP NOT NULL,
	  `last_sis_feed` TIMESTAMP NOT NULL,
	  `last_cp_feed` TIMESTAMP NOT NULL,
	  PRIMARY KEY (`database_metadata_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table permission
	--
	--						cascade NO BIT (not in the DB, but in usage)
	--		user can R/W course X				|
	--		user can R/W session X				|
	--		user can R/W offering X				v
	--
	--	can_write trumps can_read on TRUE/YES
	--

	DROP TABLE IF EXISTS `permission`;
	SET character_set_client = utf8;
	CREATE TABLE `permission` (
	  `permission_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  `can_read` TINYINT(1) NOT NULL,
	  `can_write` TINYINT(1) NOT NULL,
	  `table_row_id` INT(14) UNSIGNED NOT NULL,
	  `table_name` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL,
	  PRIMARY KEY (`permission_id`) USING BTREE,
	  UNIQUE INDEX `user_table_k` (`user_id`, `table_name`, `table_row_id`) USING BTREE
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




--
--
--
--
-- Tables to support many-to-many relationships	between primary tables
--
--
--
--




	--
	-- Table user_x_user_role
	--

	DROP TABLE IF EXISTS `user_x_user_role`;
	SET character_set_client = utf8;
	CREATE TABLE `user_x_user_role` (
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  `user_role_id` INT(3) UNSIGNED NOT NULL,
	  PRIMARY KEY (`user_id`,`user_role_id`) USING BTREE,
	  INDEX `user_x_user_role_user_id` (`user_id`),
	  INDEX `user_x_user_role_user_role_id` (`user_role_id`),
	  CONSTRAINT `fkey_user_x_user_role_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
	  CONSTRAINT `fkey_user_x_user_role_user_role_id` FOREIGN KEY (`user_role_id`) REFERENCES `user_role` (`user_role_id`) ON DELETE CASCADE ON UPDATE RESTRICT
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	--
	-- Table group_x_group
	--

	DROP TABLE IF EXISTS `group_x_group`;
	SET character_set_client = utf8;
	CREATE TABLE `group_x_group` (
	  `group_a_id` INT(14) UNSIGNED NOT NULL,
	  `group_b_id` INT(14) UNSIGNED NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table group_x_instructor
--
DROP TABLE IF EXISTS `group_x_instructor`;
CREATE TABLE `group_x_instructor` (
    `group_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`group_id`, `user_id`),
    CONSTRAINT `fkey_group_x_instructor_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_group_x_instructor_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

--
-- Table group_x_instructor_group
--
DROP TABLE IF EXISTS `group_x_instructor_group`;
CREATE TABLE `group_x_instructor_group` (
    `group_id` INT(14) UNSIGNED NOT NULL,
    `instructor_group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`group_id`, `instructor_group_id`),
    CONSTRAINT `fkey_group_x_instructor_group_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_group_x_instructor_group_instructor_group_id`
        FOREIGN KEY (`instructor_group_id`)
        REFERENCES `instructor_group` (`instructor_group_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

	--
	-- Table instructor_group_x_user
	--

	DROP TABLE IF EXISTS `instructor_group_x_user`;
	SET character_set_client = utf8;
	CREATE TABLE `instructor_group_x_user` (
	  `instructor_group_id` INT(14) UNSIGNED NOT NULL,
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  KEY `user_id_k` USING BTREE (`user_id`),
	  KEY `instructor_group_user_id_k` USING BTREE (`instructor_group_id`,`user_id`),
	  CONSTRAINT `instructor_group_x_user_ibfk_1` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`),
	  CONSTRAINT `instructor_group_x_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table group_x_user
	--

	DROP TABLE IF EXISTS `group_x_user`;
	SET character_set_client = utf8;
	CREATE TABLE `group_x_user` (
	  `group_id` INT(14) UNSIGNED NOT NULL,
	  `user_id` INT(14) UNSIGNED NOT NULL,
	PRIMARY KEY (`group_id`, `user_id`),
	  KEY `user_id_k` USING BTREE (`user_id`),
	  KEY `group_user_id_k` USING BTREE (`group_id`,`user_id`),
	  CONSTRAINT `group_x_user_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`),
	  CONSTRAINT `group_x_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table program_year_x_competency
	--

	DROP TABLE IF EXISTS `program_year_x_competency`;
	SET character_set_client = utf8;
	CREATE TABLE `program_year_x_competency` (
	  `program_year_id` INT(14) UNSIGNED NOT NULL,
	  `competency_id` INT(14) UNSIGNED NOT NULL,
	  KEY `program_year_competency_id_k` USING BTREE (`program_year_id`,`competency_id`),
	  CONSTRAINT `fkey_program_year_x_competency_prg_yr_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_program_year_x_competency_competency_id` FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table program_year_x_objective
	--

	DROP TABLE IF EXISTS `program_year_x_objective`;
	SET character_set_client = utf8;
	CREATE TABLE `program_year_x_objective` (
	  `program_year_id` INT(14) UNSIGNED NOT NULL,
	  `objective_id` INT(14) UNSIGNED NOT NULL,
	  KEY `program_year_objective_id_k` USING BTREE (`program_year_id`,`objective_id`),
	  CONSTRAINT `fkey_program_year_x_objective_prg_yr_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_program_year_x_objective_obj_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table program_year_x_discipline
	--

	DROP TABLE IF EXISTS `program_year_x_discipline`;
	SET character_set_client = utf8;
	CREATE TABLE `program_year_x_discipline` (
	  `program_year_id` INT(14) UNSIGNED NOT NULL,
	  `discipline_id` INT(14) UNSIGNED NOT NULL,
	  KEY `program_year_discipline_id_k` USING BTREE (`program_year_id`,`discipline_id`),
	  CONSTRAINT `fkey_program_year_x_discipline_prg_yr_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_program_year_x_discipline_discipline_id` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`discipline_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table program_year_director
--
DROP TABLE IF EXISTS `program_year_director`;
CREATE TABLE `program_year_director` (
    `program_year_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`program_year_id`, `user_id`),
    INDEX `fkey_program_year_director_user` (`user_id`),
    CONSTRAINT `fkey_program_year_director_program_year`
        FOREIGN KEY (`program_year_id`)
        REFERENCES `program_year` (`program_year_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE,
    CONSTRAINT `fkey_program_year_director_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB;



--
-- Table program_year_steward
--
DROP TABLE IF EXISTS `program_year_steward`;
CREATE TABLE `program_year_steward` (
    `program_year_id` INT(14) UNSIGNED NOT NULL,
    `school_id` INT(14) UNSIGNED NOT NULL,
    `department_id` INT(14) UNSIGNED,
    UNIQUE INDEX `program_year_id_school_id_department_id` (`program_year_id`, `school_id`, `department_id`),
    INDEX `fkey_program_year_steward_school` (`school_id`),
    INDEX `fkey_program_year_steward_department` (`department_id`),
    INDEX `py_s_k` (`program_year_id`, `school_id`) USING BTREE,
    CONSTRAINT `fkey_program_year_steward_department`
        FOREIGN KEY (`department_id`)
        REFERENCES `department` (`department_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE,
    CONSTRAINT `fkey_program_year_steward_program_year`
        FOREIGN KEY (`program_year_id`)
        REFERENCES `program_year` (`program_year_id`)
      ON UPDATE RESTRICT ON DELETE CASCADE,
    CONSTRAINT `fkey_program_year_steward_school`
        FOREIGN KEY (`school_id`)
        REFERENCES `school` (`school_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
DEFAULT CHARSET=utf8
ENGINE=InnoDB;



	--
	-- Table course_x_cohort
	--

	DROP TABLE IF EXISTS `course_x_cohort`;
	SET character_set_client = utf8;
	CREATE TABLE `course_x_cohort` (
	  `course_id` INT(14) UNSIGNED NOT NULL,
	  `cohort_id` INT(14) UNSIGNED NOT NULL,
	  KEY `course_cohort_id_k` USING BTREE (`course_id`,`cohort_id`),
	  CONSTRAINT `fkey_course_x_cohort_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_course_x_cohort_cohort_id` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table course_x_discipline
	--

	DROP TABLE IF EXISTS `course_x_discipline`;
	SET character_set_client = utf8;
	CREATE TABLE `course_x_discipline` (
	  `course_id` INT(14) UNSIGNED NOT NULL,
	  `discipline_id` INT(14) UNSIGNED NOT NULL,
	  KEY `course_discipline_id_k` USING BTREE (`course_id`,`discipline_id`),
	  CONSTRAINT `fkey_course_x_discipline_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_course_x_discipline_discipline_id` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`discipline_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table course_x_mesh
	--

	DROP TABLE IF EXISTS `course_x_mesh`;
	SET character_set_client = utf8;
	CREATE TABLE `course_x_mesh` (
	  `course_id` INT(14) UNSIGNED NOT NULL,
	  `mesh_descriptor_uid` VARCHAR(9) COLLATE utf8_unicode_ci NOT NULL,
	  -- can't foreign key mesh, mesh tables are myisam
	  CONSTRAINT `fkey_course_x_mesh_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table course_learning_material
	--

	DROP TABLE IF EXISTS `course_learning_material`;
	SET character_set_client = utf8;
	CREATE TABLE `course_learning_material` (
	  `course_learning_material_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `course_id` INT(14) UNSIGNED NOT NULL,
	  `learning_material_id` INT(14) UNSIGNED NOT NULL,
	  `notes` VARCHAR(500) COLLATE utf8_unicode_ci,
	  `required` TINYINT(1) NOT NULL,				-- 1 == required, 0 == recommended
	  `notes_are_public` TINYINT(1) NOT NULL,			-- 1 == can be seen in learner view, 0 == cannot
	  PRIMARY KEY (`course_learning_material_id`) USING BTREE,
	  KEY `course_lm_k` USING BTREE (`course_id`,`learning_material_id`),
	  KEY `learning_material_id_k` USING BTREE (`learning_material_id`),
	  CONSTRAINT `course_learning_material_ibfk_2` FOREIGN KEY (`learning_material_id`) REFERENCES `learning_material` (`learning_material_id`),
	  CONSTRAINT `course_learning_material_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table course_learning_material_x_mesh
	--

	DROP TABLE IF EXISTS `course_learning_material_x_mesh`;
	SET character_set_client = utf8;
	CREATE TABLE `course_learning_material_x_mesh` (
	  `course_learning_material_id` INT(14) UNSIGNED NOT NULL,
	  `mesh_descriptor_uid` VARCHAR(9) COLLATE utf8_unicode_ci NOT NULL,
	  KEY `clm_id_k` USING BTREE (`course_learning_material_id`),
      -- Note: not able to create foreign key constraint to mesh_descriptor table because it's using myisam
	  CONSTRAINT `course_learning_material_x_mesh_ibfk_1` FOREIGN KEY (`course_learning_material_id`) REFERENCES `course_learning_material` (`course_learning_material_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table course_x_objective
	--

	DROP TABLE IF EXISTS `course_x_objective`;
	SET character_set_client = utf8;
	CREATE TABLE `course_x_objective` (
	  `course_id` INT(14) UNSIGNED NOT NULL,
	  `objective_id` INT(14) UNSIGNED NOT NULL,
	  KEY `course_objective_id_k` USING BTREE (`course_id`,`objective_id`),
	  CONSTRAINT `fkey_course_x_objective_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_course_x_objective_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table course_director
--
DROP TABLE IF EXISTS `course_director`;
CREATE TABLE `course_director` (
    `course_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`course_id`, `user_id`),
    INDEX `fkey_course_director_user_id` (`user_id`),
    CONSTRAINT `fkey_course_director_course_id`
        FOREIGN KEY (`course_id`)
        REFERENCES `course` (`course_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_course_director_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

	--
	-- Table session_x_discipline
	--

	DROP TABLE IF EXISTS `session_x_discipline`;
	SET character_set_client = utf8;
	CREATE TABLE `session_x_discipline` (
		`session_id` INT(14) UNSIGNED NOT NULL,
		`discipline_id` INT(14) UNSIGNED NOT NULL,
		PRIMARY KEY (`session_id`, `discipline_id`),
		INDEX `fkey_session_x_discipline_discipline_id` (`discipline_id`),
		CONSTRAINT `fkey_session_x_discipline_session_id` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
		CONSTRAINT `fkey_session_x_discipline_discipline_id` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`discipline_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table session_learning_material
	--

	DROP TABLE IF EXISTS `session_learning_material`;
	SET character_set_client = utf8;
	CREATE TABLE `session_learning_material` (
	  `session_learning_material_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
	  `session_id` INT(14) UNSIGNED NOT NULL,
	  `learning_material_id` INT(14) UNSIGNED NOT NULL,
	  `notes` VARCHAR(500) COLLATE utf8_unicode_ci,
	  `required` TINYINT(1) NOT NULL,				-- 1 == required, 0 == recommended
	  `notes_are_public` TINYINT(1) NOT NULL,			-- 1 == can be seen in learner view, 0 == cannot
	  PRIMARY KEY (`session_learning_material_id`) USING BTREE,
	  KEY `session_lm_k` USING BTREE (`session_id`,`learning_material_id`),
	  KEY `learning_material_id_k` USING BTREE (`learning_material_id`),
	  CONSTRAINT `session_learning_material_ibfk_2` FOREIGN KEY (`learning_material_id`) REFERENCES `learning_material` (`learning_material_id`),
	  CONSTRAINT `session_learning_material_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



	--
	-- Table session_learning_material_x_mesh,
	--

	DROP TABLE IF EXISTS `session_learning_material_x_mesh`;
	SET character_set_client = utf8;
	CREATE TABLE `session_learning_material_x_mesh` (
	  `session_learning_material_id` INT(14) UNSIGNED NOT NULL,
	  `mesh_descriptor_uid` VARCHAR(9) COLLATE utf8_unicode_ci NOT NULL,
	  KEY `slm_id_k` USING BTREE (`session_learning_material_id`),
      -- Note: not able to create foreign key constraint to mesh_descriptor table because it's using myisam
	  CONSTRAINT `session_learning_material_x_mesh_ibfk_1` FOREIGN KEY (`session_learning_material_id`) REFERENCES `session_learning_material` (`session_learning_material_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table session_x_mesh
	--

	DROP TABLE IF EXISTS `session_x_mesh`;
	SET character_set_client = utf8;
	CREATE TABLE `session_x_mesh` (
		`session_id` INT(14) UNSIGNED NOT NULL,
		`mesh_descriptor_uid` VARCHAR(9) COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (`session_id`, `mesh_descriptor_uid`),
		CONSTRAINT `fkey_session_x_mesh_session_id` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table session_x_objective
	--

	DROP TABLE IF EXISTS `session_x_objective`;
	SET character_set_client = utf8;
	CREATE TABLE `session_x_objective` (
		`session_id` INT(14) UNSIGNED NOT NULL,
		`objective_id` INT(14) UNSIGNED NOT NULL,
		PRIMARY KEY (`session_id`, `objective_id`),
		INDEX `fkey_session_x_objective_objective_id` (`objective_id`),
		CONSTRAINT `fkey_session_x_objective_session_id` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE,
		CONSTRAINT `fkey_session_x_objective_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table ilm_session_facet_x_learner
--
DROP TABLE IF EXISTS `ilm_session_facet_x_learner`;
CREATE TABLE `ilm_session_facet_x_learner` (
    `ilm_session_facet_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`ilm_session_facet_id`, `user_id`),
    CONSTRAINT `fkey_ilm_session_facet_x_learner_ilm_session_facet_id`
        FOREIGN KEY (`ilm_session_facet_id`)
        REFERENCES `ilm_session_facet` (`ilm_session_facet_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_ilm_session_facet_x_learner_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

--
-- Table ilm_session_facet_x_group
--
DROP TABLE IF EXISTS `ilm_session_facet_x_group`;
CREATE TABLE `ilm_session_facet_x_group` (
  `ilm_session_facet_id` INT(14) UNSIGNED NOT NULL,
  `group_id` INT(14) UNSIGNED NOT NULL,
  PRIMARY KEY(`ilm_session_facet_id`, `group_id`),
  CONSTRAINT `fkey_ilm_session_facet_x_group_ilm_session_facet_id`
      FOREIGN KEY (`ilm_session_facet_id`)
      REFERENCES `ilm_session_facet` (`ilm_session_facet_id`)
      ON DELETE CASCADE,
  CONSTRAINT `fkey_ilm_session_facet_x_group_group_id`
      FOREIGN KEY (`group_id`)
      REFERENCES `group` (`group_id`)
      ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

--
-- Table ilm_session_facet_x_instructor
--
DROP TABLE IF EXISTS `ilm_session_facet_x_instructor`;
CREATE TABLE `ilm_session_facet_x_instructor` (
    `ilm_session_facet_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`ilm_session_facet_id`, `user_id`),
    CONSTRAINT `fkey_ilm_session_facet_x_instructor_ilm_session_facet_id`
        FOREIGN KEY (`ilm_session_facet_id`)
        REFERENCES `ilm_session_facet` (`ilm_session_facet_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_ilm_session_facet_x_instructor_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

--
-- Table ilm_session_facet_x_instructor_group
--
DROP TABLE IF EXISTS `ilm_session_facet_x_instructor_group`;
CREATE TABLE `ilm_session_facet_x_instructor_group` (
    `ilm_session_facet_id` INT(14) UNSIGNED NOT NULL,
    `instructor_group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`ilm_session_facet_id`, `instructor_group_id`),
    CONSTRAINT `fkey_ilm_session_facet_x_instructor_group_ilm_session_facet_id`
        FOREIGN KEY (`ilm_session_facet_id`)
        REFERENCES `ilm_session_facet` (`ilm_session_facet_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_ilm_session_facet_x_instructor_group_instructor_group_id`
        FOREIGN KEY (`instructor_group_id`)
        REFERENCES `instructor_group` (`instructor_group_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

	--
	-- Table offering_x_recurring_event
	--

	DROP TABLE IF EXISTS `offering_x_recurring_event`;
	SET character_set_client = utf8;
	CREATE TABLE `offering_x_recurring_event` (
	  `offering_id` INT(14) UNSIGNED NOT NULL,
	  `recurring_event_id` INT(14) UNSIGNED NOT NULL,
	  UNIQUE (`offering_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table offering_x_instructor
--
DROP TABLE IF EXISTS `offering_x_instructor`;
CREATE TABLE `offering_x_instructor` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `user_id`),
    CONSTRAINT `fkey_offering_x_instructor_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_instructor_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
) DEFAULT CHARSET='utf8'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;

--
-- Table offering_x_instructor_group
--
DROP TABLE IF EXISTS `offering_x_instructor_group`;
CREATE TABLE `offering_x_instructor_group` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `instructor_group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `instructor_group_id`),
    CONSTRAINT `fkey_offering_x_instructor_group_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_instructor_group_instructor_group_id`
        FOREIGN KEY (`instructor_group_id`)
        REFERENCES `instructor_group` (`instructor_group_id`)
) DEFAULT CHARSET='utf8'
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;

--
-- Table offering_x_learner
--
DROP TABLE IF EXISTS `offering_x_learner`;
CREATE TABLE `offering_x_learner` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `user_id`),
    CONSTRAINT `fkey_offering_x_learner_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_learner_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
) DEFAULT CHARSET='utf8'
    COLLATE='utf8_unicode_ci'
    ENGINE=InnoDB;

--
-- Table offering_x_group
--
DROP TABLE IF EXISTS `offering_x_group`;
CREATE TABLE `offering_x_group` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `group_id`),
    CONSTRAINT `fkey_offering_x_group_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_group_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
) DEFAULT CHARSET='utf8'
    COLLATE='utf8_unicode_ci'
    ENGINE=InnoDB;


	--
	-- Table objective_x_objective
	--

	DROP TABLE IF EXISTS `objective_x_objective`;
	SET character_set_client = utf8;
	CREATE TABLE `objective_x_objective` (
	  `parent_objective_id` INT(14) UNSIGNED NOT NULL,
	  `objective_id` INT(14) UNSIGNED NOT NULL,
	  KEY `objective_objective_id_k` USING BTREE (`parent_objective_id`,`objective_id`),
	  -- TODO: foreign key fails constraint test, parent_objective_id = 0, can they be null instead?
	  -- CONSTRAINT `fkey_objective_x_objective_parent_obj_id` FOREIGN KEY (`parent_objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_objective_x_objective_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table objective_x_mesh
	--

	DROP TABLE IF EXISTS `objective_x_mesh`;
	SET character_set_client = utf8;
	CREATE TABLE `objective_x_mesh` (
	  `objective_id` INT(14) UNSIGNED NOT NULL,
	  `mesh_descriptor_uid` VARCHAR(9) COLLATE utf8_unicode_ci NOT NULL,
	  -- unable to foreign key to mesh because it's myisam
	  CONSTRAINT `fkey_objective_x_mesh_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table alert_instigator
	--

	DROP TABLE IF EXISTS `alert_instigator`;
	SET character_set_client = utf8;
	CREATE TABLE `alert_instigator` (
	  `alert_id` INT(14) UNSIGNED NOT NULL,
	  `user_id` INT(14) UNSIGNED NOT NULL,
	  INDEX `alert_id` (`alert_id`),
	  INDEX `user_id` (`user_id`),
	  INDEX `alert_id_user_id` (`alert_id`,`user_id`),
	  CONSTRAINT `fkey_alert_instigator_alert_id` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE,
	  CONSTRAINT `fkey_alert_instigator_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table alert_change
	--

	DROP TABLE IF EXISTS `alert_change`;
	SET character_set_client = utf8;
	CREATE TABLE `alert_change` (
	  `alert_id` INT(14) UNSIGNED NOT NULL,
	  `alert_change_type_id` INT(14) UNSIGNED NOT NULL,
	  INDEX `alert_id` (`alert_id`),
	  INDEX `alert_id_alert_change_type_id` (`alert_id`,`alert_change_type_id`),
	  CONSTRAINT `fkey_alert_change_alert_id` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;



	--
	-- Table alert_recipient
	--

	DROP TABLE IF EXISTS `alert_recipient`;
	SET character_set_client = utf8;
	CREATE TABLE `alert_recipient` (
      `alert_id` int(14) unsigned NOT NULL,
      `school_id` int(14) unsigned NOT NULL,
      KEY `alert_id_school_id` (`alert_id`,`school_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table user_sync_exception
--
DROP TABLE IF EXISTS `user_sync_exception`;
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

-- Table user_x_cohort
DROP TABLE IF EXISTS `user_x_cohort`;
CREATE TABLE `user_x_cohort` (
    `user_id` INT(14) UNSIGNED NOT NULL,
    `cohort_id` INT(14) UNSIGNED NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`, `cohort_id`),
    INDEX `fkey_user_x_cohort_cohort` (`cohort_id`),
    CONSTRAINT `fkey_user_x_cohort_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_user_x_cohort_cohort` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table curriculum_inventory_institution
DROP TABLE IF EXISTS `curriculum_inventory_institution`;
CREATE TABLE `curriculum_inventory_institution` (
    `school_id` INT(10) UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `aamc_code` VARCHAR(10) NOT NULL,
    `address_street` VARCHAR(100) NOT NULL,
    `address_city` VARCHAR(100) NOT NULL,
    `address_state_or_province` VARCHAR(50) NOT NULL,
    `address_zipcode` VARCHAR(10) NOT NULL,
    `address_country_code` CHAR(2) NOT NULL,
    PRIMARY KEY (`school_id`),
    CONSTRAINT `fkey_curriculum_inventory_institution_school_id`
        FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table curriculum_inventory_report
DROP TABLE IF EXISTS `curriculum_inventory_report`;
CREATE TABLE `curriculum_inventory_report` (
    `report_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_id` INT(10) UNSIGNED NOT NULL,
    `year` SMALLINT(4) UNSIGNED NOT NULL,
    `name` VARCHAR(200) NULL DEFAULT NULL,
    `description` TEXT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    PRIMARY KEY (`report_id`),
    UNIQUE INDEX `program_id_year` (`program_id`, `year`),
    CONSTRAINT `fkey_curriculum_inventory_report_program_id`
        FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table curriculum_inventory_sequence
DROP TABLE IF EXISTS `curriculum_inventory_sequence`;
CREATE TABLE `curriculum_inventory_sequence` (
    `report_id` INT(10) UNSIGNED NOT NULL,
    `description` TEXT NULL,
    PRIMARY KEY (`report_id`),
    CONSTRAINT `fkey_curriculum_inventory_sequence_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table curriculum_inventory_academic_level
DROP TABLE IF EXISTS `curriculum_inventory_academic_level`;
CREATE TABLE `curriculum_inventory_academic_level` (
    `academic_level_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_id` INT(10) UNSIGNED NOT NULL,
    `level` INT(2) UNSIGNED NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    PRIMARY KEY (`academic_level_id`),
    UNIQUE INDEX `report_id_level` (`report_id`, `level`),
    CONSTRAINT `fkey_curriculum_inventory_academic_level_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table curriculum_inventory_sequence_block
DROP TABLE IF EXISTS `curriculum_inventory_sequence_block`;
CREATE TABLE `curriculum_inventory_sequence_block` (
    `sequence_block_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_id` INT(10) UNSIGNED NOT NULL,
    `required` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `child_sequence_order` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `order_in_sequence` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `minimum` INT(11) NOT NULL DEFAULT '-1',
    `maximum` INT(11) NOT NULL DEFAULT '-1',
    `track` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    `description` TEXT NULL,
    `title` VARCHAR(200) NOT NULL,
    `start_date` DATE NULL DEFAULT NULL,
    `end_date` DATE NULL DEFAULT NULL,
    `academic_level_id` INT(10) UNSIGNED NOT NULL,
    `duration` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `course_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    `parent_sequence_block_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (`sequence_block_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_report_id` (`report_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_course_id` (`course_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_parent_id` (`parent_sequence_block_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_academic_level_id` (`academic_level_id`),
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_academic_level_id`
        FOREIGN KEY (`academic_level_id`) REFERENCES `curriculum_inventory_academic_level` (`academic_level_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_course_id`
        FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_parent_id`
        FOREIGN KEY (`parent_sequence_block_id`) REFERENCES `curriculum_inventory_sequence_block` (`sequence_block_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table curriculum_inventory_export
DROP TABLE IF EXISTS `curriculum_inventory_export`;
CREATE TABLE `curriculum_inventory_export` (
    `report_id` INT(10) UNSIGNED NOT NULL,
    `document` MEDIUMTEXT NOT NULL,
    `created_by` INT(10) UNSIGNED NOT NULL,
    `created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`report_id`),
    CONSTRAINT `fkey_curriculum_inventory_export_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_curriculum_inventory_export_user_id`
        FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`)
        ON UPDATE RESTRICT ON DELETE NO ACTION
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table aamc_pcrs
DROP TABLE IF EXISTS `aamc_pcrs`;
CREATE TABLE `aamc_pcrs` (
    `pcrs_id` VARCHAR(21) NOT NULL,
    `description` TEXT NOT NULL,
    PRIMARY KEY (`pcrs_id`)
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table aamc_method
DROP TABLE IF EXISTS `aamc_method`;
CREATE TABLE `aamc_method` (
    `method_id` VARCHAR(10) NOT NULL,
    `description` TEXT NOT NULL,
    PRIMARY KEY (`method_id`)
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table session_type_x_aamc_method
DROP TABLE IF EXISTS `session_type_x_aamc_method`;
CREATE TABLE `session_type_x_aamc_method` (
    `session_type_id` INT(14) UNSIGNED NOT NULL,
    `method_id` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`session_type_id`),
    UNIQUE INDEX `session_type_id_method_id` (`session_type_id`, `method_id`),
    INDEX `aamc_method_id_fkey` (`method_id`),
    CONSTRAINT `aamc_method_id_fkey`
        FOREIGN KEY (`method_id`) REFERENCES `aamc_method` (`method_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `session_type_id_fkey`
        FOREIGN KEY (`session_type_id`) REFERENCES `session_type` (`session_type_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Table competency_x_aamc_pcrs
DROP TABLE IF EXISTS `competency_x_aamc_pcrs`;
CREATE TABLE `competency_x_aamc_pcrs` (
    `competency_id` INT(14) UNSIGNED NOT NULL,
    `pcrs_id` VARCHAR(21) NOT NULL,
    PRIMARY KEY (`competency_id`, `pcrs_id`),
    INDEX `aamc_pcrs_id_fkey` (`pcrs_id`),
    CONSTRAINT `aamc_pcrs_id_fkey`
        FOREIGN KEY (`pcrs_id`) REFERENCES `aamc_pcrs` (`pcrs_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `competency_id_fkey`
        FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

--
--
--
-- Required for CodeIgniter happiness
--
--
--
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
    `version` INT(3) NOT NULL
)
DEFAULT CHARSET=utf8
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

DROP TABLE IF EXISTS `ci_sessions`;
SET character_set_client = utf8;

CREATE TABLE IF NOT EXISTS  `ci_sessions` (
    session_id varchar(40) DEFAULT '0' NOT NULL,
    ip_address varchar(45) DEFAULT '0' NOT NULL,
    user_agent varchar(120) NOT NULL,
    last_activity int(10) unsigned DEFAULT 0 NOT NULL,
    user_data text NOT NULL,
    PRIMARY KEY (session_id),
    KEY `last_activity_idx` (`last_activity`)
)
DEFAULT CHARSET=utf8
ENGINE=MyISAM;
