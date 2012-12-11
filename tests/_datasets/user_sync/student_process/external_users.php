<?php
/**
 * This file contains a sole array consisting of test user data
 * which can be fed into an "Array" user source
 * for the student-user sync process unit-test.
 *
 * @see Ilios_UserSync_UserSource_Array
 * @see Ilios_UserSync_Process_StudentProcess
 * @see Ilios_UserSync_Process_StudentProcessTest
 */

/**
 * A nested array of arrays, representing external user records.
 * Each sub-array is expected to contain the following properties
 * 'first_name'      ... (string) the user's first name
 * 'last_name'       ... (string) the user's last name
 * 'middle_name'     ... (string) the user's middle name
 * 'email'           ... (string) the user's email address
 * 'phone'           ... (string) the user's phone number
 * 'is_student'      ... (boolean) the user's "student status". TRUE if the user is a student, otherwise FALSE
 * 'school_id'       ... (int) the user's primary school affiliation, ID of that school.
 * 'graduation_year' ... (int) expected graduation year of the student
 * 'uid'             ... (string) the user's (UC-wide) unique ID
 * @var array
 */
$externalUsers = array(
    // TEST CASE: new user
    // ILIOS USER ID: 101 (post-import)
    //
    // This external user record will be successfully be ingested into Ilios,
    // resulting in a new student record.
    array(
        'first_name' => 'Leah',
        'last_name' => 'Learner',
        'middle_name' => 'New',
        'email' => 'Leah.Learner@test.com',
        'phone' => '111-111-1112',
        'is_student' => true,
        'school_id' => 3,
        'graduation_year' => 2022,
        'uid' => 'xxxx111112'
    ),

    // TEST CASE: user update
    // ILIOS USER ID: 100
    //
    // This user will be successfully ingested and its
    // corresponding user entry in Ilios will get updated.
    array(
        'first_name' => 'Stu',
        'last_name' => 'Student',
        'middle_name' => 'Der',
        'email' => 'stuart.student@test.com',
        'phone' => '111-111-1111',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2012,
        'uid' => 'xxxx111111'
    ),

    // TEST CASE: existing user with student-status mismatch
    // External user is student, Ilios user is non-student.
    // ILIOS USER ID: 90
    //
    // ingesting this user will result in a user sync exception
    // due to a student-status mismatch
    array(
        'first_name' => 'Mike',
        'last_name' => 'Mismatch',
        'middle_name' => 'Status',
        'email' => 'mike.s.mismatch@test.com',
        'phone' => '111-111-1113',
        'is_student' => true,
        'school_id' => 2,
        'graduation_year' => 2022,
        'uid' => 'xxxx111113'
    ),

    // TEST CASE: existing user with email mismatch
    // ILIOS USER ID: 91
    //
    // ingesting this user will result in a user sync exception
    // due to a student-status mismatch
    array(
        'first_name' => 'Miriam',
        'last_name' => 'Mismatch',
        'middle_name' => 'Email',
        'email' => 'miriam.e.mismatch@test.com',
        'phone' => '111-111-1114',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2012,
        'uid' => 'xxxx111114'
    ),

    // TEST CASE: existing user with student-status mismatch
    // External user is non-student, Ilios user is student.
    // see Redmine Ticket 879
    // ILIOS USER ID: 92
    //
    // This corresponding Ilios user will be flagged for review and not updated.
    array(
        'first_name' => 'Niles',
        'last_name' => 'Nonstudent',
        'middle_name' => '',
        'email' => 'niles.nonstudent@test.com',
        'phone' => '111-111-1115',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => 2022,
        'uid' => 'xxxx111115'
    ),

    // TEST CASE: external user validation error - invalid email
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Ivan',
        'last_name' => 'Invalid',
        'middle_name' => 'Email',
        'email' => '',  // no email
        'phone' => '555-555-5551',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2010,
        'uid' => 'xxxx511111'
    ),

    // TEST CASE: external user validation error - invalid email
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Ivanka',
        'last_name' => 'Invalid',
        'middle_name' => 'Email',
        'email' => 'THISISNOTANEMAILADDRESS',  // invalid email
        'phone' => '555-555-5552',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2010,
        'uid' => 'xxxx511112'
    ),

    // TEST CASE: external user validation error - invalid school
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Irina',
        'last_name' => 'Invalid',
        'middle_name' => 'School',
        'email' => 'irina.s.Invalid@test.com',
        'phone' => '555-555-5553',
        'is_student' => true,
        'school_id' => -1, // invalid school id
        'graduation_year' => 2033,
        'uid' => 'xxxx511113'
    ),

    // TEST CASE: external user validation error - invalid school
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Immanuel',
        'last_name' => 'Invalid',
        'middle_name' => 'School',
        'email' => 'immanuel.s.Invalid@test.com',
        'phone' => '555-555-5554',
        'is_student' => true,
        'school_id' => null, // invalid school id
        'graduation_year' => 2034,
        'uid' => 'xxxx511113'
    ),


    // TEST CASE: external user validation error - invalid UID
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Ingrid',
        'last_name' => 'Invalid',
        'middle_name' => 'Uid',
        'email' => 'ingrid.u.invalid@test.com',
        'phone' => '555-555-5555',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2014,
        'uid' => '' // missing uid
    ),

    // TEST CASE: external user validation error - invalid UID
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Ilya',
        'last_name' => 'Invalid',
        'middle_name' => 'Uid',
        'email' => 'ilya.u.invalid@test.com',
        'phone' => '555-555-5556',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2022,
        'uid' => null // missing uid
    ),

    // TEST CASE: external user validation error - invalid email
    // *AND*
    // TEST CASE: unexamined user - unknown error
    // (see corresponding Ilios user with user_id = 71)
    array(
        'first_name' => 'Ulf',
        'last_name' => 'Unexamined',
        'middle_name' => 'Unknown',
        'email' => 'JUNKHERE', // invalid email
        'phone' => '444-444-4445',
        'is_student' => true,
        'school_id' => 2,
        'graduation_year' => 2017,
        'uid' => 'xxxx444445' // matches Ilios user with user_id = 71
    ),

    // TEST CASE: student flagged to be ignored / no update
    // TEST CASE: student disabled / no update
    // ILIOS USER ID: 30 and 31
    array (
        'first_name' => 'Igor',
        'last_name' => 'Ignored',
        'middle_name' => null,
        'email' => 'igor.ignored@test.com',
        'phone' => '333-333-3333',
        'is_student' => true,
        'school_id' => 1,
        'graduation_year' => 2015,
        'uid' => 'xxxx333333' // matches Ilios users with user_id = 30 and 31
    ),

    // TEST CASE: external user validation error - invalid school code
    // see Redmine Ticket 864
    //
    // This user will not get ingested by the process.
    array(
        'first_name' => 'Iris',
        'last_name' => 'Invalid',
        'middle_name' => 'School',
        'email' => 'iris.s.ilvalid@test.com',
        'phone' => '555-555-5557',
        'is_student' => true,
        'school_id' => -1,
        'graduation_year' => 2022,
        'uid' => 'xxxx555557'
    )
);
