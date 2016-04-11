<?php
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

/**
 * course_rollover.php
 *
 * rolls over a given course given its course_id and academic start year
 *
 */

//database connection settings
$db_name = 'ilios_prod';
$db_user = 'ilios';
$db_pass = 'tr0yt0y';
$db_host = '64.54.142.204';
$db_port = 3306;


$link = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($link->connect_errno) {
    echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
}

//course id of the original course to rollover
$courseId = 851;
//start year of the new course
$startYear = 2016;

//GET ORIGINAL COURSE INFO
$query = sprintf("SELECT course_id, course_level, year, start_date, end_date, locked, archived, school_id, clerkship_type_id, title, external_id, published FROM course WHERE course_id = %d", $courseId);
if ($stmt = mysqli_prepare($link, $query)) {

    /* execute statement */
    mysqli_stmt_execute($stmt);

    /* bind result variables */
    mysqli_stmt_bind_result($stmt, $course_id, $course_level, $year, $start_date, $end_date, $locked, $archived, $school_id, $clerkship_type_id, $title, $external_id, $published);

    /* fetch/set the necessary values */
    while (mysqli_stmt_fetch($stmt)) {
        $orig_course_id  = $course_id;
        $orig_year = $year;
        $year_diff = $startYear - $orig_year;
        //To make sure we end up on the same day, even on leap years, let's use '52 weeks' to represent a year.
        $interval_in_weeks = ($year_diff * 52);
        $orig_start_date = $start_date;
        $orig_start_date_year = date('Y', strtotime($start_date));
        $orig_end_date   = $end_date;
        $orig_end_date_year = date('Y', strtotime($end_date));
    }

    /* close statement */
    mysqli_stmt_close($stmt);
}

//debugging output...
echo "-- Original Course Id =>" . $orig_course_id . "\n";
echo "-- Original Start Year =>" . $orig_year . "\n";
echo "-- Original Start date =>" . $orig_start_date . "\n";
echo "-- Original End date =>" . $orig_end_date . "\n";
echo "-- Year difference =>" . $year_diff . "\n";
echo "-- Original start_date year =>" . $orig_start_date_year . "\n";
echo "-- Original end_date year =>" . $orig_end_date_year . "\n";

//handle the extra shifting -- MAY NOT BE NECESSARY!!!!
//$start_date_year_diff = ($orig_start_date_year == $orig_year) ? 0 : $orig_start_date_year - $orig_year;
//$end_date_year_diff = ($orig_end_date_year == $orig_year) ? 0 : $orig_end_date_year - $orig_year;
//$start_date_year_interval = $year_diff + $start_date_year_diff;
//$end_date_year_interval = $year_diff + $end_date_year_diff;
//echo "Start date should be shifted by an additional +" . $start_date_year_diff . " years (+" . $start_date_year_interval . " years total)" . "\n";
//echo "End date should be shifted by an additional +" . $end_date_year_diff . " years (+" . $end_date_year_interval . " years total)" . "\n";
//echo "Start date year interval should be +" . $start_year_interval . " years." . "\n";
//echo "End date year interval increase +" . $end_year_interval . " years." . "\n";


//INSERT NEW COURSE using the input arguments and the values above
$query = sprintf("INSERT INTO course (course_level, year, start_date, end_date, locked, archived, school_id, published_as_tbd, clerkship_type_id, title) SELECT course_level, %d AS `year`, DATE_ADD('%s', INTERVAL + %d WEEK) AS `start_date`, DATE_ADD('%s', INTERVAL + %d WEEK) AS `end_date`, 0 AS `locked`, 0 AS `archived`, school_id, published_as_tbd, clerkship_type_id, title FROM course WHERE course_id = %d", $startYear, $orig_start_date, $interval_in_weeks, $orig_end_date, $interval_in_weeks, $orig_course_id);
print $query . ";\n";
if ($stmt = mysqli_prepare($link, $query)) {
    mysqli_stmt_execute($stmt);
    $new_course_id = mysqli_insert_id($link);
    print "-- (new course id: " . $new_course_id . ")" . "\n";
    mysqli_stmt_close($stmt);
}
//debugging
//print "This is the new course id =>" . $new_course_id;

//INSERT COURSE DIRECTORS
$query = sprintf("INSERT INTO course_director SELECT %d, user_id FROM course_director WHERE course_id = %d", $new_course_id, $orig_course_id);
print $query . ";\n";
if ($stmt = mysqli_prepare($link, $query)) {
    if (mysqli_stmt_execute($stmt)){
        //echo "Director(s) added to course!" ."\n";
        mysqli_stmt_close($stmt);
    }
}


//COURSE LEARNING MATERIALS
$query = sprintf("INSERT INTO course_learning_material (course_id, learning_material_id, notes, required, notes_are_public) SELECT %d, learning_material_id, notes, required, notes_are_public FROM course_learning_material WHERE course_id = %d", $new_course_id, $orig_course_id);
print $query . ";\n";
if ($stmt = mysqli_prepare($link, $query)) {
    if (mysqli_stmt_execute($stmt)){
        //echo "Learning Materials added to course!" ."\n";
        mysqli_stmt_close($stmt);
    }
}

//COURSE TOPICS
$query = sprintf("INSERT INTO course_x_discipline SELECT %d, discipline_id FROM course_x_discipline WHERE course_id = %d", $new_course_id, $orig_course_id);
print $query . ";\n";
if ($stmt = mysqli_prepare($link, $query)) {
    if (mysqli_stmt_execute($stmt)){
        //echo "Topics added to course!" ."\n";
        mysqli_stmt_close($stmt);
    }
}

//COURSE MESH
$query = sprintf("INSERT INTO course_x_mesh SELECT %d, mesh_descriptor_uid FROM course_x_mesh WHERE course_id = %d", $new_course_id, $orig_course_id);
print $query . ";\n";
if ($stmt = mysqli_prepare($link, $query)) {
    if (mysqli_stmt_execute($stmt)){
        //echo "MeSH terms added to course!" ."\n";
        mysqli_stmt_close($stmt);
    }
}

//COURSE OBJECTIVES
$query = sprintf("INSERT INTO course_x_objective SELECT %d, objective_id FROM course_x_objective WHERE course_id = %d", $new_course_id, $orig_course_id);
print $query . ";\n";
if ($stmt = mysqli_prepare($link, $query)) {
    if (mysqli_stmt_execute($stmt)){
        //echo "Objectives added to course!" ."\n";
        mysqli_stmt_close($stmt);
    }
}


//SESSIONS

//GET THE OLD SESSION IDs
//create an array to track the original session id and the corresponding new one
$orig_session_ids = array();
//loop through the sessions for the original course and create new sessions for each
$query = sprintf("SELECT session_id FROM session WHERE course_id = %d", $orig_course_id);
if ($stmt = mysqli_prepare($link, $query)) {

    /* execute statement */
    mysqli_stmt_execute($stmt);

    /* bind result variables */
    mysqli_stmt_bind_result($stmt, $orig_session_id);

    /* fetch values */
    while (mysqli_stmt_fetch($stmt)) {

        //add all the original session id's to the array.
        $orig_session_ids[] = $orig_session_id;

    }

    /* close statement */
    mysqli_stmt_close($stmt);
}

//INSERT NEW SESSIONS
//loop through the original session id's and insert a new session based on the original
$orig_and_new_session_id_array = array();
foreach ($orig_session_ids as $orig_session_id){
    $query = sprintf("INSERT INTO session (session_type_id, course_id, title, attire_required, equipment_required, supplemental, published_as_tbd, last_updated_on) SELECT session_type_id, %d, title, attire_required, equipment_required, supplemental, published_as_tbd, NOW() FROM session WHERE session_id = %d", $new_course_id, $orig_session_id);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        /* execute statement */
        mysqli_stmt_execute($stmt);
        $new_session_id = mysqli_insert_id($link);
        print "-- (new session id: " . $new_session_id . ")" . "\n";
        $orig_and_new_session_id_array[$orig_session_id] = $new_session_id;
        mysqli_stmt_close($stmt);
    };
}

//Session
//use the orig/new session id pairs to add the session descriptions, learning materials, topics, objectives, and mesh terms
//Session Description
foreach ($orig_and_new_session_id_array as $old => $new){
    $query = sprintf("INSERT INTO session_description (session_id, description) SELECT %d, description FROM session_description WHERE session_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        /* execute statement */
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    };
}

//Session learning materials
foreach ($orig_and_new_session_id_array as $old => $new){
    $query = sprintf("INSERT INTO session_learning_material (session_id, learning_material_id, notes, required, notes_are_public) SELECT %d, learning_material_id, notes, required, notes_are_public FROM session_learning_material WHERE session_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        /* execute statement */
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    };
}

//Session topics (disciplines)
foreach ($orig_and_new_session_id_array as $old => $new){
    $query = sprintf("INSERT INTO session_x_discipline SELECT %d, discipline_id FROM session_x_discipline WHERE session_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        /* execute statement */
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    };
}

//Session MeSH Terms
foreach ($orig_and_new_session_id_array as $old => $new){
    $query = sprintf("INSERT INTO session_x_mesh SELECT %d, mesh_descriptor_uid FROM session_x_mesh WHERE session_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        /* execute statement */
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    };
}

//Session Objectives
foreach ($orig_and_new_session_id_array as $old => $new){
    $query = sprintf("INSERT INTO session_x_objective SELECT %d, objective_id FROM session_x_objective WHERE session_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        /* execute statement */
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    };
}


//OFFERINGS
//GET THE ORIGINAL OFFERING IDs
$orig_offering_ids = array();
//loop through the sessions for the original course and create new sessions for each
foreach ($orig_session_ids as $orig_session_id) {
    $query = sprintf("SELECT offering_id FROM offering WHERE session_id = %d", $orig_session_id);
    //print $query . ";\n";
    if ($stmt = mysqli_prepare($link, $query)) {

        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $orig_offering_id);
        while (mysqli_stmt_fetch($stmt)) {

            //add all the original session id's to the array.
            $orig_offering_ids[$orig_session_id][] = $orig_offering_id;

        }

        /* close statement */
        mysqli_stmt_close($stmt);
    }
}

//INSERT NEW OFFERINGS
$new_session_and_new_offering_array = array();
$orig_offering_id_and_new_offering_id_array = array();
foreach ($orig_offering_ids as $orig_session_id => $orig_offering_array){

    foreach ($orig_offering_array as $orig_offering_id){
        $new_session_id = $orig_and_new_session_id_array[$orig_session_id];
        $query = sprintf("INSERT INTO offering (room, session_id, start_date, end_date, last_updated_on) SELECT room, %d, DATE_ADD(start_date, INTERVAL + %d WEEK), DATE_ADD(end_date, INTERVAL + %d WEEK), NOW() FROM offering WHERE offering_id = %d", $new_session_id, $interval_in_weeks, $interval_in_weeks, $orig_offering_id);
        print $query . ";\n";
        if($stmt = mysqli_prepare($link, $query)) {
            // execute statement
            mysqli_stmt_execute($stmt);
            $new_offering_id = mysqli_insert_id($link);
            print "-- (new offering id: " . $new_offering_id . ")" . "\n";
            //add the original offering id and the corresponding new offering id to a an array.
            $orig_offering_id_and_new_offering_id_array[$orig_offering_id] =
            $new_session_and_new_offering_array[$new_session_id] = $new_offering_id;
            mysqli_stmt_close($stmt);
        }
    }
}

//ADD INSTRUCTORS TO THE OFFERINGS
foreach ($orig_offering_id_and_new_offering_id_array as $old => $new) {
    $query = sprintf("INSERT INTO offering_x_instructor SELECT %d, user_id FROM offering_x_instructor WHERE offering_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        // execute statement
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

//ADD INSTRUCTOR GROUPS TO THE OFFERING
foreach ($orig_offering_id_and_new_offering_id_array as $old => $new) {
    $query = sprintf("INSERT INTO offering_x_instructor_group SELECT %d, instructor_group_id FROM offering_x_instructor_group WHERE offering_id = %d", $new, $old);
    print $query . ";\n";
    if($stmt = mysqli_prepare($link, $query)) {
        // execute statement
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}




/* close connection */
mysqli_close($link);

print "\n";
print "-- Rollover complete. This is the new course id =>" . $new_course_id . "\n\n";
