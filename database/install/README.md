# Database installation

The database must be created using `latin1_swedish_ci` as default collation and `latin1` as default character set.

The database may be constructed by `source`-ing the table schemas, stored procedures and stored functions SQL.

To create the needed tables, stored procedures/functions and triggers, run the following the SQL files against your database:

    ilios2_tables.sql
    ilios2_stored.sql
    ilios2_triggers.sql

## Import default data

Once you've created the database, you should populate the database with the default data.

1. Extract the `data_population/mesh_tables_and_data.sql.gz` file containing the MeSH universe data.

2. Run the SQL scripts in the `data_population/` directory against your database to import the default data in the following order:

    data_population/alert_change_type_data.sql
    data_population/SOM_competency_data.sql
    data_population/SOM_discipline_data.sql
    data_population/learning_material_data.sql
    data_population/school_data.sql
    data_population/session_type_data.sql
    data_population/user_role_data.sql
    data_population/course_clerkship_type_data.sql
    data_population/mesh_tables_and_data.sql
    data_population/SOP_competency_data.sql
    data_population/SOP_discipline_data.sql

## Shortcut Alternative

You may source the utility script `database/make_new_i2_database.sql` to create the database, set its schema and to ingest the default data population in one step. _Please read the code comments within the file and replace the placeholder for the database name accordingly before sourcing the script!_

Also, please note that you still have to extract the archive containing the MeSH universe beforehand.
