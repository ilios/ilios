
--
--
-- This creates the stored functions and procedures used by the Ilios application.
--
--
--
--
--


DROP FUNCTION IF EXISTS root_group_of_group;
DELIMITER //
	CREATE FUNCTION root_group_of_group (in_gid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE gid INT DEFAULT in_gid;
		DECLARE pgid INT DEFAULT 0;

		WHILE gid IS NOT NULL DO
			SELECT parent_group_id
				INTO pgid
				FROM `group`
				WHERE group_id = gid;

			IF pgid IS NULL THEN
				RETURN gid;
			ELSE
				SET gid = pgid;
			END IF;
		END WHILE;

		RETURN 0;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS group_is_child_of_group;
DELIMITER //
	CREATE FUNCTION group_is_child_of_group (in_potential_child_gid INT, in_potential_parent_gid INT)
		RETURNS TINYINT
		READS SQL DATA
	BEGIN
		DECLARE gid INT DEFAULT in_potential_child_gid;
		DECLARE pgid INT DEFAULT 0;

		IF in_potential_child_gid = in_potential_parent_gid THEN
			RETURN 0;
		END IF;

		WHILE gid IS NOT NULL DO
			SELECT parent_group_id
				INTO pgid
				FROM `group`
				WHERE group_id = gid;

			IF pgid = in_potential_parent_gid THEN
				RETURN 1;
			ELSE
				SET gid = pgid;
			END IF;
		END WHILE;

		RETURN 0;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS user_can_be_assigned;
DELIMITER //
	CREATE FUNCTION user_can_be_assigned (in_user_id INT, in_master_group_id INT)
		RETURNS TINYINT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE all_group_id INT DEFAULT 0;
		DECLARE user_gid INT DEFAULT 0;
		DECLARE flag INT DEFAULT 0;
		DECLARE gid_cursor CURSOR FOR SELECT group_id FROM group_x_user WHERE user_id = in_user_id;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN gid_cursor;

		REPEAT
			FETCH gid_cursor INTO user_gid;

			IF NOT out_of_rows THEN
				IF user_gid = in_master_group_id THEN
					SET flag = 0;
				ELSE
					SELECT group_is_child_of_group(user_gid, in_master_group_id)
						INTO flag;
				END IF;

				IF flag THEN
					CLOSE gid_cursor;

					RETURN 1;
				END IF;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE gid_cursor;

		RETURN 0;
	END;
	//
DELIMITER ;


DROP PROCEDURE IF EXISTS user_ids_from_cohort_and_master_group;
DELIMITER //
	CREATE PROCEDURE user_ids_from_cohort_and_master_group (in_user_count INT, in_cohort_id INT, in_group_id INT)
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE tmp_uid INT DEFAULT 0;
		DECLARE flag INT DEFAULT 0;
		DECLARE inserted_count INT DEFAULT 0;
		DECLARE uid_cursor CURSOR FOR SELECT u.`user_id` FROM `user` u JOIN `user_x_cohort` uxc ON uxc.`user_id` = u.`user_id` WHERE uxc.`cohort_id` = in_cohort_id ORDER BY RAND();
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		CREATE TEMPORARY TABLE IF NOT EXISTS tt_subgroup (uid INT);


		OPEN uid_cursor;

		REPEAT
			FETCH uid_cursor INTO tmp_uid;

			IF NOT out_of_rows THEN
				SELECT user_can_be_assigned(tmp_uid, in_group_id)
					INTO flag;

				IF NOT flag THEN
					INSERT INTO tt_subgroup (uid) VALUES (tmp_uid);

					SET inserted_count = inserted_count + 1;
				END IF;
			END IF;

		UNTIL out_of_rows OR inserted_count = in_user_count END REPEAT;

		CLOSE uid_cursor;


		SELECT * FROM tt_subgroup;
		DROP TABLE tt_subgroup;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS program_has_year_stewarded_by_school;
DELIMITER //
	CREATE FUNCTION program_has_year_stewarded_by_school (in_pid INT, in_sid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE pyid INT DEFAULT 0;
		DECLARE flag INT DEFAULT 0;
		DECLARE pyid_cursor CURSOR FOR SELECT program_year_id FROM program_year WHERE program_id = in_pid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN pyid_cursor;

		REPEAT
			FETCH pyid_cursor INTO pyid;

			IF NOT out_of_rows THEN
				SELECT count(*) FROM program_year_steward WHERE program_year_id = pyid AND school_id = in_sid
					INTO flag;

				IF (flag > 0) THEN
					CLOSE pyid_cursor;

					RETURN 1;
				END IF;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE pyid_cursor;

		RETURN 0;
	END;
	//
DELIMITER ;



DROP PROCEDURE IF EXISTS programs_with_title_restricted_by_school_for_user;
DELIMITER //
	CREATE PROCEDURE programs_with_title_restricted_by_school_for_user (in_title_query VARCHAR(30), in_school_id INT, in_user_id INT)
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE prog_id INT DEFAULT 0;
		DECLARE program_owner_school_id INT DEFAULT 0;
		DECLARE flag INT DEFAULT 0;
		DECLARE count INT DEFAULT 0;
		DECLARE pid_cursor CURSOR FOR SELECT program_id, owning_school_id FROM program WHERE deleted = 0 AND title LIKE in_title_query;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		CREATE TEMPORARY TABLE IF NOT EXISTS tt_programs (`program_id` INT(14), `title` VARCHAR(60) COLLATE utf8_unicode_ci, `short_title` VARCHAR(10) COLLATE utf8_unicode_ci, `publish_event_id` INT(14) UNSIGNED, `duration` TINYINT(1) UNSIGNED, `deleted` TINYINT(1), `owning_school_id` INT(10) UNSIGNED, `published_as_tbd` TINYINT(1));


		OPEN pid_cursor;

		REPEAT
			FETCH pid_cursor INTO prog_id, program_owner_school_id;

			IF NOT out_of_rows THEN
				IF program_owner_school_id = in_school_id THEN
					INSERT INTO tt_programs SELECT * FROM program WHERE program_id = prog_id;
				ELSE
					SET flag = 0;

					SELECT count(can_write) FROM permission WHERE user_id = in_user_id AND table_name = 'program' AND table_row_id = prog_id
						INTO count;

					IF (count > 0) THEN
						SELECT can_write FROM permission WHERE user_id = in_user_id AND table_name = 'program' AND table_row_id = prog_id
							INTO flag;
					END IF;

					IF flag THEN
						INSERT INTO tt_programs SELECT * FROM program WHERE program_id = prog_id;
					ELSE
						SELECT program_has_year_stewarded_by_school(prog_id, in_school_id)
							INTO flag;

						SET out_of_rows = 0;

						IF flag THEN
							INSERT INTO tt_programs SELECT * FROM program WHERE program_id = prog_id;
						END IF;
					END IF;
				END IF;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE pid_cursor;


		SELECT * FROM tt_programs ORDER BY `title`;
		DROP TABLE tt_programs;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS course_has_cohort_stewarded_or_owned_by_school;
DELIMITER //
	CREATE FUNCTION course_has_cohort_stewarded_or_owned_by_school (in_cid INT, in_sid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE cohid INT DEFAULT 0;
		DECLARE flag INT DEFAULT 0;
		DECLARE pyid INT DEFAULT 0;
		DECLARE program_owning_school_id INT DEFAULT 0;
		DECLARE cohid_cursor CURSOR FOR SELECT cohort_id FROM course_x_cohort WHERE course_id = in_cid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN cohid_cursor;

		REPEAT
			FETCH cohid_cursor INTO cohid;

			IF NOT out_of_rows THEN
				SELECT program_year_id FROM cohort WHERE cohort_id = cohid
					INTO pyid;

				SELECT count(*) FROM program_year_steward WHERE program_year_id = pyid AND school_id = in_sid
					INTO flag;

				IF (flag > 0) THEN
					SELECT program.owning_school_id FROM program, program_year WHERE program_year.program_year_id = pyid AND program.program_id = program_year.program_id
						INTO program_owning_school_id;

					IF program_owning_school_id = in_sid THEN
						SET flag = 1;
					END IF;
				END IF;

				IF flag THEN
					CLOSE cohid_cursor;

					RETURN 1;
				END IF;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE cohid_cursor;

		RETURN 0;
	END;
	//
DELIMITER ;


DROP PROCEDURE IF EXISTS courses_with_title_restricted_by_school_for_user;
DELIMITER //
    CREATE PROCEDURE `courses_with_title_restricted_by_school_for_user`(IN `in_title_query` VARCHAR(30), IN `in_school_id` INT, IN `in_user_id` INT)
        LANGUAGE SQL
        NOT DETERMINISTIC
        READS SQL DATA
        SQL SECURITY DEFINER
        COMMENT ''
    BEGIN
        DECLARE out_of_rows INT DEFAULT 0;
        DECLARE cid INT DEFAULT 0;
        DECLARE course_owner_school_id INT DEFAULT 0;
        DECLARE flag INT DEFAULT 0;
        DECLARE count INT DEFAULT 0;
        DECLARE cid_cursor CURSOR FOR
           SELECT course_id, owning_school_id
           FROM course
           WHERE deleted = 0 AND archived = 0 AND title LIKE in_title_query;
        DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

        CREATE TEMPORARY TABLE IF NOT EXISTS tt_courses (
            `course_id` INT(14) UNSIGNED,
            `title` VARCHAR(100) COLLATE utf8_unicode_ci,
            `publish_event_id` INT(14) UNSIGNED,
            `course_level` SMALLINT(2) UNSIGNED,
            `year` SMALLINT(4) UNSIGNED,
            `start_date` DATE,
            `end_date` DATE,
            `deleted` TINYINT(1),
            `external_id` VARCHAR(18) COLLATE utf8_unicode_ci,
            `locked` TINYINT(1),
            `archived` TINYINT(1),
            `owning_school_id` INT(10) UNSIGNED,
            `published_as_tbd` TINYINT(1),
            `clerkship_type_id` INT(10) UNSIGNED
        );

        OPEN cid_cursor;

        REPEAT
            FETCH cid_cursor INTO cid, course_owner_school_id;

            IF NOT out_of_rows THEN
                IF course_owner_school_id = in_school_id THEN
                    INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                ELSE
                    SET flag = 0;

                    SELECT count(can_write)
                    FROM permission
                    WHERE user_id = in_user_id AND table_name = 'course' AND table_row_id = cid
                    INTO count;

                    IF (count > 0) THEN
                        SELECT can_write
                        FROM permission
                        WHERE user_id = in_user_id AND table_name = 'course' AND table_row_id = cid
                        INTO flag;
                    END IF;

                    IF flag THEN
                        INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                    ELSE
                        SELECT course_has_cohort_stewarded_or_owned_by_school(cid, in_school_id)
                        INTO flag;

                        SET out_of_rows = 0;

                        IF flag THEN
                            INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                        END IF;
                    END IF;
                END IF;
            END IF;
        UNTIL out_of_rows END REPEAT;

        CLOSE cid_cursor;

        SELECT *
        FROM tt_courses
        ORDER BY `title`, `start_date`, `end_date`;

        DROP TABLE tt_courses;
    END;
//
DELIMITER ;

DROP PROCEDURE IF EXISTS mesh_search;
DELIMITER //
	CREATE PROCEDURE mesh_search (in_search_term VARCHAR(60))
		READS SQL DATA
	BEGIN
		CREATE TEMPORARY TABLE IF NOT EXISTS tt_mesh_search (tree_number VARCHAR(41), uid VARCHAR(9),
								     name VARCHAR(192), matching_table VARCHAR(36),
								     matching_table_uid VARCHAR(9),
								     PRIMARY KEY (tree_number) USING BTREE);


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Descriptor Name',
				    mesh_descriptor.mesh_descriptor_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor
			   WHERE ((MATCH(mesh_descriptor.name) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Descriptor Annotation',
				    mesh_descriptor.mesh_descriptor_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor
			   WHERE ((MATCH(mesh_descriptor.annotation) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Descriptor Previous Indexing',
				    mesh_descriptor.mesh_descriptor_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor, mesh_previous_indexing
			   WHERE ((MATCH(mesh_previous_indexing.previous_indexing) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_previous_indexing.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid)
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Semantic Type Name',
				    mesh_semantic_type.mesh_semantic_type_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor, mesh_descriptor_x_concept, mesh_concept_x_semantic_type, mesh_semantic_type
			   WHERE ((MATCH(mesh_semantic_type.name) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_semantic_type.mesh_semantic_type_uid = mesh_concept_x_semantic_type.mesh_semantic_type_uid)
				    AND (mesh_concept_x_semantic_type.mesh_concept_uid = mesh_descriptor_x_concept.mesh_concept_uid)
				    AND (mesh_descriptor_x_concept.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid)
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Term Name',
				    mesh_term.mesh_term_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor, mesh_descriptor_x_concept, mesh_concept_x_term, mesh_term
			   WHERE ((MATCH(mesh_term.name) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_term.mesh_term_uid = mesh_concept_x_term.mesh_term_uid)
				    AND (mesh_concept_x_term.mesh_concept_uid = mesh_descriptor_x_concept.mesh_concept_uid)
				    AND (mesh_descriptor_x_concept.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid)
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Concept Name',
				    mesh_concept.mesh_concept_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor, mesh_descriptor_x_concept, mesh_concept
			   WHERE ((MATCH(mesh_concept.name) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_concept.mesh_concept_uid = mesh_descriptor_x_concept.mesh_concept_uid)
				    AND (mesh_descriptor_x_concept.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid)
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Concept Scope Note',
				    mesh_concept.mesh_concept_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor, mesh_descriptor_x_concept, mesh_concept
			   WHERE ((MATCH(mesh_concept.scope_note) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_concept.mesh_concept_uid = mesh_descriptor_x_concept.mesh_concept_uid)
				    AND (mesh_descriptor_x_concept.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid)
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		INSERT IGNORE INTO tt_mesh_search (tree_number, uid, name, matching_table, matching_table_uid)
			SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
				    mesh_tree_x_descriptor.mesh_descriptor_uid AS uid,
				    mesh_descriptor.name AS name,
				    'Concept CASN',
				    mesh_concept.mesh_concept_uid AS matching_uid
			   FROM mesh_tree_x_descriptor, mesh_descriptor, mesh_descriptor_x_concept, mesh_concept
			   WHERE ((MATCH(mesh_concept.casn_1_name) AGAINST (in_search_term IN BOOLEAN MODE))
				    AND (mesh_concept.mesh_concept_uid = mesh_descriptor_x_concept.mesh_concept_uid)
				    AND (mesh_descriptor_x_concept.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid)
				    AND (mesh_descriptor.mesh_descriptor_uid = mesh_tree_x_descriptor.mesh_descriptor_uid));


		SELECT * FROM tt_mesh_search;
		DROP TABLE tt_mesh_search;
	END;
	//
DELIMITER ;


DROP PROCEDURE IF EXISTS decompose_mesh_tree;
DELIMITER //
	CREATE PROCEDURE decompose_mesh_tree (in_mesh_tree_string VARCHAR(41))
		READS SQL DATA
	BEGIN
		DECLARE length INT DEFAULT 3;
		DECLARE string_length INT;
		DECLARE substring VARCHAR(41);

		CREATE TEMPORARY TABLE IF NOT EXISTS tt_decomposed_mesh_tree (tree_number VARCHAR(41), name VARCHAR(192));

		SELECT LENGTH(in_mesh_tree_string) INTO string_length;

		WHILE length <= string_length DO

			SELECT SUBSTRING(in_mesh_tree_string, 1, length) INTO substring;

			INSERT INTO tt_decomposed_mesh_tree (tree_number, name)
				SELECT mesh_tree_x_descriptor.tree_number AS tree_number,
					    mesh_descriptor.name AS name
				   FROM mesh_tree_x_descriptor, mesh_descriptor
				   WHERE ((mesh_tree_x_descriptor.tree_number = substring)
					    AND (mesh_tree_x_descriptor.mesh_descriptor_uid = mesh_descriptor.mesh_descriptor_uid));

			SET length = length + 4;

		END WHILE;


		SELECT * FROM tt_decomposed_mesh_tree;
		DROP TABLE tt_decomposed_mesh_tree;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_cohorts_from_course_to_course;
DELIMITER //
	CREATE FUNCTION copy_cohorts_from_course_to_course (in_original_cid INT, in_new_cid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE cid INT DEFAULT 0;
		DECLARE rows_added INT DEFAULT 0;
		DECLARE cid_cursor CURSOR FOR SELECT cohort_id FROM course_x_cohort WHERE course_id = in_original_cid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN cid_cursor;

		REPEAT
			FETCH cid_cursor INTO cid;

			IF NOT out_of_rows THEN
				INSERT INTO course_x_cohort (course_id, cohort_id) VALUES (in_new_cid, cid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE cid_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_disciplines_from_course_to_course;
DELIMITER //
	CREATE FUNCTION copy_disciplines_from_course_to_course (in_original_cid INT, in_new_cid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE did INT DEFAULT 0;
		DECLARE rows_added INT DEFAULT 0;
		DECLARE did_cursor CURSOR FOR SELECT discipline_id FROM course_x_discipline WHERE course_id = in_original_cid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN did_cursor;

		REPEAT
			FETCH did_cursor INTO did;

			IF NOT out_of_rows THEN
				INSERT INTO course_x_discipline (course_id, discipline_id) VALUES (in_new_cid, did);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE did_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_directors_from_course_to_course;
DELIMITER //
	CREATE FUNCTION copy_directors_from_course_to_course (in_original_cid INT, in_new_cid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE uid INT DEFAULT 0;
		DECLARE rows_added INT DEFAULT 0;
		DECLARE uid_cursor CURSOR FOR SELECT user_id FROM course_director WHERE course_id = in_original_cid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN uid_cursor;

		REPEAT
			FETCH uid_cursor INTO uid;

			IF NOT out_of_rows THEN
				INSERT INTO course_director (course_id, user_id) VALUES (in_new_cid, uid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE uid_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_mesh_from_course_to_course;
DELIMITER //
	CREATE FUNCTION copy_mesh_from_course_to_course (in_original_cid INT, in_new_cid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE mdid VARCHAR(9);
		DECLARE rows_added INT DEFAULT 0;
		DECLARE mdid_cursor CURSOR FOR SELECT mesh_descriptor_uid FROM course_x_mesh WHERE course_id = in_original_cid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN mdid_cursor;

		REPEAT
			FETCH mdid_cursor INTO mdid;

			IF NOT out_of_rows THEN
				INSERT INTO course_x_mesh (course_id, mesh_descriptor_uid) VALUES (in_new_cid, mdid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE mdid_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS copy_learning_material_mesh_from_course_lm_to_course_lm;
DELIMITER //
	CREATE FUNCTION copy_learning_material_mesh_from_course_lm_to_course_lm (in_original_clmid INT, in_new_clmid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE mdid VARCHAR(9);
		DECLARE rows_added INT DEFAULT 0;
		DECLARE mdid_cursor CURSOR FOR SELECT mesh_descriptor_uid FROM course_learning_material_x_mesh WHERE course_learning_material_id = in_original_clmid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN mdid_cursor;

		REPEAT
			FETCH mdid_cursor INTO mdid;

			IF NOT out_of_rows THEN
				INSERT INTO course_learning_material_x_mesh (course_learning_material_id, mesh_descriptor_uid) VALUES (in_new_clmid, mdid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE mdid_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS copy_objective_attributes_to_objective;
DELIMITER //
	CREATE FUNCTION copy_objective_attributes_to_objective (in_original_oid INT, in_new_oid INT, in_copy_parents INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE poid INT DEFAULT 0;
		DECLARE mdid VARCHAR(9);
		DECLARE rows_added INT DEFAULT 0;
		DECLARE oxo_cursor CURSOR FOR SELECT parent_objective_id FROM objective_x_objective WHERE objective_id = in_original_oid;
		DECLARE oxm_cursor CURSOR FOR SELECT mesh_descriptor_uid FROM objective_x_mesh WHERE objective_id = in_original_oid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		IF in_copy_parents THEN
			OPEN oxo_cursor;

			REPEAT
				FETCH oxo_cursor INTO poid;

				IF NOT out_of_rows THEN
					INSERT INTO objective_x_objective (objective_id, parent_objective_id) VALUES (in_new_oid, poid);

					SET rows_added = rows_added + 1;
				END IF;

			UNTIL out_of_rows END REPEAT;

			CLOSE oxo_cursor;
		END IF;


		SET out_of_rows = 0;
		OPEN oxm_cursor;

		REPEAT
			FETCH oxm_cursor INTO mdid;

			IF NOT out_of_rows THEN
				INSERT INTO objective_x_mesh (objective_id, mesh_descriptor_uid) VALUES (in_new_oid, mdid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE oxm_cursor;


		RETURN rows_added;
	END;
	//
DELIMITER ;


DROP FUNCTION IF EXISTS copy_disciplines_from_session_to_session;
DELIMITER //
	CREATE FUNCTION copy_disciplines_from_session_to_session (in_original_sid INT, in_new_sid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE did INT DEFAULT 0;
		DECLARE rows_added INT DEFAULT 0;
		DECLARE did_cursor CURSOR FOR SELECT discipline_id FROM session_x_discipline WHERE session_id = in_original_sid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN did_cursor;

		REPEAT
			FETCH did_cursor INTO did;

			IF NOT out_of_rows THEN
				INSERT INTO session_x_discipline (session_id, discipline_id) VALUES (in_new_sid, did);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE did_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_mesh_session_to_session;
DELIMITER //
	CREATE FUNCTION copy_mesh_session_to_session (in_original_sid INT, in_new_sid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE mdid VARCHAR(9);
		DECLARE rows_added INT DEFAULT 0;
		DECLARE mdid_cursor CURSOR FOR SELECT mesh_descriptor_uid FROM session_x_mesh WHERE session_id = in_original_sid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN mdid_cursor;

		REPEAT
			FETCH mdid_cursor INTO mdid;

			IF NOT out_of_rows THEN
				INSERT INTO session_x_mesh (session_id, mesh_descriptor_uid) VALUES (in_new_sid, mdid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE mdid_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_learning_material_mesh_from_session_lm_to_session_lm;
DELIMITER //
	CREATE FUNCTION copy_learning_material_mesh_from_session_lm_to_session_lm (in_original_slmid INT, in_new_slmid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE mdid VARCHAR(9);
		DECLARE rows_added INT DEFAULT 0;
		DECLARE mdid_cursor CURSOR FOR SELECT mesh_descriptor_uid FROM session_learning_material_x_mesh WHERE session_learning_material_id = in_original_slmid;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		OPEN mdid_cursor;

		REPEAT
			FETCH mdid_cursor INTO mdid;

			IF NOT out_of_rows THEN
				INSERT INTO session_learning_material_x_mesh (session_learning_material_id, mesh_descriptor_uid) VALUES (in_new_slmid, mdid);

				SET rows_added = rows_added + 1;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE mdid_cursor;

		RETURN rows_added;
	END;
	//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_ilm_session_attributes_to_ilm_session;
DELIMITER //
CREATE FUNCTION copy_ilm_session_attributes_to_ilm_session (in_original_ilmsfid INT, in_new_ilmsfid INT)
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE out_of_rows INT DEFAULT 0;
    DECLARE uid INT DEFAULT 0;
    DECLARE gid INT DEFAULT 0;
    DECLARE rows_added INT DEFAULT 0;
    DECLARE i_cursor CURSOR FOR SELECT user_id FROM ilm_session_facet_x_instructor WHERE ilm_session_facet_id = in_original_ilmsfid;
    DECLARE j_cursor CURSOR FOR SELECT instructor_group_id FROM ilm_session_facet_x_instructor_group WHERE ilm_session_facet_id = in_original_ilmsfid;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

    OPEN i_cursor;

    REPEAT
        FETCH i_cursor INTO uid;

        IF NOT out_of_rows THEN
            INSERT INTO ilm_session_facet_x_instructor (ilm_session_facet_id, user_id) VALUES (in_new_ilmsfid, uid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE i_cursor;

    SET out_of_rows = 0;

    OPEN j_cursor;

    REPEAT
        FETCH j_cursor INTO gid;

        IF NOT out_of_rows THEN
            INSERT INTO ilm_session_facet_x_instructor_group (ilm_session_facet_id, instructor_group_id) VALUES (in_new_ilmsfid, gid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE j_cursor;

    RETURN rows_added;
END;
//
DELIMITER ;

DROP FUNCTION IF EXISTS copy_offering_attributes_to_offering;
DELIMITER //
CREATE FUNCTION copy_offering_attributes_to_offering (in_original_oid INT, in_new_oid INT)
    RETURNS INT
    READS SQL DATA
BEGIN
    DECLARE out_of_rows INT DEFAULT 0;
    DECLARE uid INT DEFAULT 0;
    DECLARE gid INT DEFAULT 0;
    DECLARE rows_added INT DEFAULT 0;
    DECLARE i_cursor CURSOR FOR SELECT user_id  FROM offering_x_instructor WHERE offering_id = in_original_oid;
    DECLARE j_cursor CURSOR FOR SELECT instructor_group_id FROM offering_x_instructor_group WHERE offering_id = in_original_oid;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

    OPEN i_cursor;

    REPEAT
        FETCH i_cursor INTO uid;

        IF NOT out_of_rows THEN
            INSERT INTO offering_x_instructor (offering_id, user_id) VALUES (in_new_oid, uid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE i_cursor;

    SET out_of_rows = 0;

    OPEN j_cursor;

    REPEAT
        FETCH j_cursor INTO gid;

        IF NOT out_of_rows THEN
            INSERT INTO offering_x_instructor_group (offering_id, instructor_group_id) VALUES (in_new_oid, gid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE j_cursor;

    RETURN rows_added;
END;
//
DELIMITER ;



DROP PROCEDURE IF EXISTS nuke_course;
DELIMITER //
	CREATE PROCEDURE nuke_course (in_course_id INT)
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE sid INT DEFAULT 0;
		DECLARE session_cursor CURSOR FOR SELECT session_id FROM session WHERE course_id = in_course_id;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;


		OPEN session_cursor;

		REPEAT
			SET out_of_rows = 0;
			FETCH session_cursor INTO sid;

			IF NOT out_of_rows THEN
				CALL nuke_session(sid);
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE session_cursor;


		DELETE FROM course_director WHERE course_id = in_course_id;
		DELETE FROM course_x_cohort WHERE course_id = in_course_id;
		DELETE FROM course_x_discipline WHERE course_id = in_course_id;
		DELETE FROM course_x_mesh WHERE course_id = in_course_id;

		CALL nuke_learning_material_associations(in_course_id, 'course');

		CALL nuke_objective_associations(in_course_id, 'course');


		DELETE FROM course WHERE course_id = in_course_id;
	END;
	//
DELIMITER ;



DROP PROCEDURE IF EXISTS nuke_session;
DELIMITER //
CREATE PROCEDURE nuke_session (in_session_id INT)
READS SQL DATA
BEGIN
    DECLARE out_of_rows INT DEFAULT 0;
    DECLARE ilm_id INT DEFAULT 0;
    DECLARE oid INT DEFAULT 0;
    DECLARE offering_cursor CURSOR FOR SELECT offering_id FROM offering WHERE session_id = in_session_id;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

    DELETE FROM session_description WHERE session_id = in_session_id;
    DELETE FROM session_x_discipline WHERE session_id = in_session_id;
    DELETE FROM session_x_mesh WHERE session_id = in_session_id;

    CALL nuke_learning_material_associations(in_session_id, 'session');

    CALL nuke_objective_associations(in_session_id, 'session');

    SELECT ilm_session_facet_id FROM session WHERE session_id = in_session_id INTO ilm_id;


    OPEN offering_cursor;

    REPEAT
        SET out_of_rows = 0;
        FETCH offering_cursor INTO oid;

        IF NOT out_of_rows THEN
            CALL nuke_offering(oid);
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE offering_cursor;

    DELETE FROM session WHERE session_id = in_session_id;


    IF ilm_id IS NOT NULL THEN
        DELETE FROM ilm_session_facet WHERE ilm_session_facet_id = ilm_id;
    END IF;
END;
//
DELIMITER ;


DROP PROCEDURE IF EXISTS nuke_objective_associations;
DELIMITER //
	CREATE PROCEDURE nuke_objective_associations (in_unique_id INT, in_parent_name VARCHAR(60))
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE pkid INT DEFAULT 0;
		DECLARE view_cursor CURSOR FOR SELECT * FROM tmp_obj_view;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		SET @query = CONCAT('CREATE VIEW tmp_obj_view AS SELECT objective_id FROM ', in_parent_name, '_x_objective WHERE ', in_parent_name, '_id = ', in_unique_id);
		PREPARE statement FROM @query;
		EXECUTE statement;
		DEALLOCATE PREPARE statement;


		OPEN view_cursor;

		REPEAT
			SET out_of_rows = 0;
			FETCH view_cursor INTO pkid;

			IF NOT out_of_rows THEN
				DELETE FROM objective_x_objective WHERE parent_objective_id = pkid;
				DELETE FROM objective_x_objective WHERE objective_id = pkid;
				DELETE FROM objective_x_mesh WHERE objective_id = pkid;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE view_cursor;

		SET @query = CONCAT('DELETE FROM ', in_parent_name, '_x_objective WHERE ', in_parent_name, '_id = ', in_unique_id);
		PREPARE statement FROM @query;
		EXECUTE statement;
		DEALLOCATE PREPARE statement;

		DROP VIEW tmp_obj_view;
	END;
	//
DELIMITER ;


DROP PROCEDURE IF EXISTS nuke_learning_material_associations;
DELIMITER //
	CREATE PROCEDURE nuke_learning_material_associations (in_unique_id INT, in_parent_name VARCHAR(60))
		READS SQL DATA
	BEGIN
		DECLARE out_of_rows INT DEFAULT 0;
		DECLARE pkid INT DEFAULT 0;
		DECLARE view_cursor CURSOR FOR SELECT * FROM tmp_lm_view;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

		SET @query = CONCAT('CREATE VIEW tmp_lm_view AS SELECT ', in_parent_name, '_learning_material_id FROM ', in_parent_name, '_learning_material WHERE ', in_parent_name, '_id = ', in_unique_id);
		PREPARE statement FROM @query;
		EXECUTE statement;
		DEALLOCATE PREPARE statement;


		OPEN view_cursor;

		REPEAT
			SET out_of_rows = 0;
			FETCH view_cursor INTO pkid;

			IF NOT out_of_rows THEN
				SET @query = CONCAT('DELETE FROM ', in_parent_name, '_learning_material_x_mesh WHERE ', in_parent_name, '_learning_material_id = ', pkid);
				PREPARE statement FROM @query;
				EXECUTE statement;
				DEALLOCATE PREPARE statement;
			END IF;

		UNTIL out_of_rows END REPEAT;

		CLOSE view_cursor;

		DROP VIEW tmp_lm_view;

		SET @query = CONCAT('DELETE FROM ', in_parent_name, '_learning_material WHERE ', in_parent_name, '_id = ', in_unique_id);
		PREPARE statement FROM @query;
		EXECUTE statement;
		DEALLOCATE PREPARE statement;
	END;
	//
DELIMITER ;


DROP PROCEDURE IF EXISTS nuke_offering;
DELIMITER //
CREATE PROCEDURE nuke_offering (in_offering_id INT)
    READS SQL DATA
BEGIN
    DECLARE recurring_event_id INT DEFAULT 0;
    DECLARE select_found_match INT DEFAULT 1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET select_found_match = 0;


    DELETE FROM offering_x_learner WHERE offering_id = in_offering_id;
    DELETE FROM offering_x_group WHERE offering_id = in_offering_id;
    DELETE FROM offering_x_instructor WHERE offering_id = in_offering_id;
    DELETE FROM offering_x_instructor_group WHERE offering_id = in_offering_id;

    SELECT recurring_event_id FROM offering_x_recurring_event WHERE offering_id = in_offering_id
        INTO recurring_event_id;

    IF select_found_match THEN
        CALL nuke_recurring_event_chain(recurring_event_id);
        DELETE FROM offering_x_recurring_event WHERE offering_id = in_offering_id;
    END IF;

    DELETE FROM offering WHERE offering_id = in_offering_id;
END;
    //
DELIMITER ;


DROP PROCEDURE IF EXISTS nuke_recurring_event_chain;
DELIMITER //
	CREATE PROCEDURE nuke_recurring_event_chain (in_recurring_event_id INT)
		READS SQL DATA
	BEGIN
		DECLARE next_recurring_event_id INT DEFAULT 0;
		DECLARE select_found_match INT DEFAULT 1;
		DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET select_found_match = 0;


		SELECT next_recurring_event_id FROM recurring_event WHERE recurring_event_id = in_recurring_event_id
			INTO next_recurring_event_id;

		IF select_found_match THEN
			CALL nuke_recurring_event_chain(next_recurring_event_id);
		END IF;

		DELETE FROM recurring_event WHERE recurring_event_id = in_recurring_event_id;
	END;
	//
DELIMITER ;
