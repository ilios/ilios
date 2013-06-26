/*
  This creates and pre-loads a new database instance.

  Prerequisites:

  - Substitute the placeholder X's with an appropriate database name.

  Notes:

  It is assumed that this script is run from within the /database directory of this Ilios codebase. If not then the paths to the sourced SQL scripts must be adjusted.

  Instructions:

    sed -i 's/XXXXXX/ilios_database_name/g' make_new_ilios_database.sql
    mysql -u ilios -p <  make_new_ilios_database.sql
*/

CREATE DATABASE XXXXXX DEFAULT CHARACTER SET = latin1 DEFAULT COLLATE = latin1_swedish_ci;
USE XXXXXX;
SOURCE ilios_tables.sql;
SOURCE ilios_stored.sql;
SOURCE ilios_triggers.sql;
SOURCE data_population/alert_change_type_data.sql;
SOURCE data_population/SOM_competency_data.sql;
SOURCE data_population/SOM_discipline_data.sql;
SOURCE data_population/learning_material_data.sql;
SOURCE data_population/school_data.sql;
SOURCE data_population/assessment_option_data.sql;
SOURCE data_population/session_type_data.sql;
SOURCE data_population/user_role_data.sql;
SOURCE data_population/course_clerkship_type_data.sql;
SOURCE data_population/mesh_tables_and_data.sql;
SOURCE data_population/SOP_competency_data.sql;
SOURCE data_population/SOP_discipline_data.sql;
SOURCE data_population/curriculum_inventory.sql;
