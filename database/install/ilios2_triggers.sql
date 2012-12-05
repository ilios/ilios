/**
 * Creates triggers on various tables in the Ilios database schema.
 */

-- drop existing triggers
DROP TRIGGER IF EXISTS `trig_ilm_session_facet_instructor_post_delete`;
DROP TRIGGER IF EXISTS `trig_ilm_session_facet_instructor_post_insert`;
DROP TRIGGER IF EXISTS `trig_ilm_session_facet_learner_post_delete`;
DROP TRIGGER IF EXISTS `trig_ilm_session_facet_learner_post_insert`;
DROP TRIGGER IF EXISTS `trig_ilm_session_facet_post_update`;
DROP TRIGGER IF EXISTS `trig_learning_material_post_update`;
DROP TRIGGER IF EXISTS `trig_objective_post_update`;
DROP TRIGGER IF EXISTS `trig_offering_pre_update`;
DROP TRIGGER IF EXISTS `trig_offering_instructor_post_delete`;
DROP TRIGGER IF EXISTS `trig_offering_instructor_post_insert`;
DROP TRIGGER IF EXISTS `trig_offering_learner_post_delete`;
DROP TRIGGER IF EXISTS `trig_offering_learner_post_insert`;
DROP TRIGGER IF EXISTS `trig_session_description_post_delete` ;
DROP TRIGGER IF EXISTS `trig_session_description_post_insert`;
DROP TRIGGER IF EXISTS `trig_session_description_post_update`;
DROP TRIGGER IF EXISTS `trig_session_learning_material_post_delete` ;
DROP TRIGGER IF EXISTS `trig_session_learning_material_post_insert`;
DROP TRIGGER IF EXISTS `trig_session_learning_material_post_update`;
DROP TRIGGER IF EXISTS `trig_session_learning_material_x_mesh_post_delete`;
DROP TRIGGER IF EXISTS `trig_session_learning_material_x_mesh_post_insert`;
DROP TRIGGER IF EXISTS `trig_session_pre_update`;
DROP TRIGGER IF EXISTS `trig_session_x_discipline_post_delete`;
DROP TRIGGER IF EXISTS `trig_session_x_discipline_post_insert`;
DROP TRIGGER IF EXISTS `trig_session_x_mesh_post_delete`;
DROP TRIGGER IF EXISTS `trig_session_x_mesh_post_insert`;
DROP TRIGGER IF EXISTS `trig_session_x_objective_post_delete`;
DROP TRIGGER IF EXISTS `trig_session_x_objective_post_insert`;

delimiter $$

-- DELETE trigger on "ilm_session_facet_instructor"
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_ilm_session_facet_instructor_post_delete` AFTER DELETE ON `ilm_session_facet_instructor` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`ilm_session_facet_id` = OLD.`ilm_session_facet_id`;
END;

-- INSERT trigger on "ilm_session_facet_instructor"
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_ilm_session_facet_instructor_post_insert` AFTER INSERT ON `ilm_session_facet_instructor` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`ilm_session_facet_id` = NEW.`ilm_session_facet_id`;
END;

-- DELETE trigger on "ilm_session_facet_learner"
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_ilm_session_facet_learner_post_delete` AFTER DELETE ON `ilm_session_facet_learner` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`ilm_session_facet_id` = OLD.`ilm_session_facet_id`;
END;

-- INSERT trigger on "ilm_session_facet_learner"
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_ilm_session_facet_learner_post_insert` AFTER INSERT ON `ilm_session_facet_learner` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`ilm_session_facet_id` = NEW.`ilm_session_facet_id`;
END;

-- UPDATE trigger on "ilm_session_facet" table
-- Sets the "last_updated_on" to the current time on associated sessions
-- if the given records were modified.
CREATE TRIGGER `trig_ilm_session_facet_post_update` AFTER UPDATE ON `ilm_session_facet`
FOR EACH ROW BEGIN
	IF (NEW.hours <> OLD.hours || NEW.due_date <> OLD.due_date) THEN
		UPDATE `session` SET `session`.`last_updated_on` = NOW()
		WHERE `session`.`ilm_session_facet_id` = NEW.`ilm_session_facet_id`;
	END IF;
END;

-- UPDATE trigger on "learning_material" table
-- Sets the "last_updated_on" to the current time on associated sessions 
-- if the given records were modified.
CREATE TRIGGER `trig_learning_material_post_update` AFTER UPDATE ON `learning_material`
FOR EACH ROW BEGIN
	IF (NEW.`learning_material_status_id` <> OLD.`learning_material_status_id`) THEN
		UPDATE `session` SET `session`.last_updated_on = NOW()
		WHERE `session`.`session_id` IN (
			SELECT `session_id` FROM `session_learning_material`
			WHERE `session_learning_material`.`learning_material_id` = NEW.`learning_material_id`
		);
	END IF;
END;

-- UPDATE trigger on "objective" table
-- Sets the "last_updated_on" to the current time on associated sessions 
-- if the given records were modified.
CREATE TRIGGER `trig_objective_post_update` AFTER UPDATE ON `objective` FOR EACH ROW BEGIN
	IF (NEW.`title` <> OLD.`title` 
		|| NEW.`competency_id` <> OLD.`competency_id`) THEN
		UPDATE `session` SET `session`.`last_updated_on` = NOW()
		WHERE `session`.`session_id` IN (
			SELECT `session_id` FROM `session_x_objective`
			WHERE `session_x_objective`.`objective_id` = NEW.`objective_id`
		);
	END IF;
END;

-- DELETE trigger on "offering_instructor"
-- Sets the "last_updated_on" to the current time on the parent record in the "offering" table.
CREATE TRIGGER `trig_offering_instructor_post_delete` AFTER DELETE ON `offering_instructor`
FOR EACH ROW BEGIN
	UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
	WHERE `offering`.`offering_id` = OLD.`offering_id`;
END;

-- INSERT trigger on "offering_instructor"
-- Sets the "last_updated_on" to the current time on the parent record in the "offering" table.
CREATE TRIGGER `trig_offering_instructor_post_insert` AFTER INSERT ON `offering_instructor`
FOR EACH ROW BEGIN
	UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
	WHERE `offering`.`offering_id` = NEW.`offering_id`;
END;

-- DELETE trigger on "offering_learner"
-- Sets the "last_updated_on" to the current time on the parent record in the "offering" table.
CREATE TRIGGER `trig_offering_learner_post_delete` AFTER DELETE ON `offering_learner`
FOR EACH ROW BEGIN
	UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
	WHERE `offering`.`offering_id` = OLD.`offering_id`;
END;

-- INSERT trigger on "offering_learner"
-- Sets the "last_updated_on" to the current time on the parent record in the "offering" table.
CREATE TRIGGER `trig_offering_learner_post_insert` AFTER INSERT ON `offering_learner`
FOR EACH ROW BEGIN
	UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
	WHERE `offering`.`offering_id` = NEW.`offering_id`;
END;

-- UPDATE trigger on "session" table
-- Sets the "last_updated_on" to the current time if the given records were modified.
CREATE TRIGGER `trig_session_pre_update` BEFORE UPDATE ON `session` 
FOR EACH ROW BEGIN
	IF (NEW.`title` <> OLD.`title`
		|| NEW.`publish_event_id` <> OLD.`publish_event_id`
		|| NEW.`attire_required` <> OLD.`attire_required`
		|| NEW.`equipment_required` <> OLD.`equipment_required`
		|| NEW.`supplemental` <> OLD.`supplemental`
		|| NEW.`session_type_id` <> OLD.`session_type_id`
		|| NEW.`deleted` <> OLD.`deleted`
		|| NEW.`ilm_session_facet_id` <> OLD.`ilm_session_facet_id`
		|| NEW.`published_as_tbd` <> OLD.`published_as_tbd`) THEN
		SET NEW.`last_updated_on` = NOW();
	END IF;
END;

-- DELETE trigger ON "session_description" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_description_post_delete` AFTER DELETE ON `session_description` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = OLD.`session_id`;
END;

-- INSERT trigger ON "session_description" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_description_post_insert` AFTER INSERT ON `session_description` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = NEW.`session_id`;
END;

-- UPDATE trigger ON "session_description" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_description_post_update` AFTER UPDATE ON `session_description` 
FOR EACH ROW BEGIN
	IF (NEW.`description` <> OLD.`description`) THEN
		UPDATE `session` SET `session`.`last_updated_on` = NOW()
		WHERE `session`.`session_id` = NEW.`session_id`;
	END IF;
END;

-- DELETE trigger ON "session_learning_material" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_learning_material_post_delete` AFTER DELETE ON `session_learning_material` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = OLD.`session_id`;
END;

-- INSERT trigger ON "session_learning_material" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_learning_material_post_insert` AFTER INSERT ON `session_learning_material` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = NEW.`session_id`;
END;

-- UPDATE trigger ON "session_learning_material_x_mesh" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_learning_material_post_update` AFTER UPDATE ON `session_learning_material` 
FOR EACH ROW BEGIN
	IF (NEW.`notes` <> OLD.`notes`
		|| NEW.`required` <> OLD.`required`
		|| NEW.`notes_are_public` <> OLD.`notes_are_public`) THEN
		UPDATE `session` SET `session`.`last_updated_on` = NOW()
		WHERE `session`.`session_id` = NEW.`session_id`;
	END IF;
END;

-- DELETE trigger ON "session_learning_material_x_mesh" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_learning_material_x_mesh_post_delete` AFTER DELETE ON `session_learning_material_x_mesh` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` IN (
		SELECT `session_id` FROM `session_learning_material`
		WHERE `session_learning_material`.`session_learning_material_id` = OLD.`session_learning_material_id`
	);
END;

-- INSERT trigger ON "session_learning_material_x_mesh" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_learning_material_x_mesh_post_insert` AFTER INSERT ON `session_learning_material_x_mesh` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` IN (
		SELECT `session_id` FROM `session_learning_material`
		WHERE `session_learning_material`.`session_learning_material_id` = NEW.`session_learning_material_id`
	);
END;

-- UPDATE trigger on "offering" table
-- Sets the "last_updated_on" to the current time if the given records were modified.
CREATE TRIGGER `trig_offering_pre_update` BEFORE UPDATE ON `offering` 
FOR EACH ROW BEGIN
	IF (NEW.`room` <> OLD.`room`
		|| NEW.`publish_event_id` <> OLD.`publish_event_id`
		|| NEW.`start_date` <> OLD.`start_date`
		|| NEW.`end_date` <> OLD.`end_date`
		|| NEW.`deleted` <> OLD.`deleted`) THEN 
		SET NEW.`last_updated_on` = NOW();
	END IF;
END;

-- DELETE trigger ON "session_x_discipline" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_x_discipline_post_delete` AFTER DELETE ON `session_x_discipline` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = OLD.`session_id`;
END;

-- INSERT trigger ON "session_x_discipline" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_x_discipline_post_insert` AFTER INSERT ON `session_x_discipline` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = NEW.`session_id`;
END;

-- DELETE trigger ON "session_x_mesh" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_x_mesh_post_delete` AFTER DELETE ON `session_x_mesh` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = OLD.`session_id`;
END;

-- INSERT trigger ON "session_x_mesh" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_x_mesh_post_insert` AFTER INSERT ON `session_x_mesh` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = NEW.`session_id`;
END;

-- DELETE trigger ON "session_x_objective" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_x_objective_post_delete` AFTER DELETE ON `session_x_objective` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = OLD.`session_id`;
END;

-- INSERT trigger ON "session_x_objective" table.
-- Sets the "last_updated_on" to the current time on the parent record in the "session" table.
CREATE TRIGGER `trig_session_x_objective_post_insert` AFTER INSERT ON `session_x_objective` 
FOR EACH ROW BEGIN
	UPDATE `session` SET `session`.`last_updated_on` = NOW()
	WHERE `session`.`session_id` = NEW.`session_id`;
END;

$$

delimiter ;