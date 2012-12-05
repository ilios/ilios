<?php
/**
 * This file contains a sole array consisting of test user data
 * which can be fed into an "Array" user source
 * for the non-student-user sync process unit-test.
 *
 * @see Ilios2_UserSync_UserSource_Array
 * @see Ilios2_UserSync_Process_NonStudentProcess
 * @see Ilios2_UserSync_Process_NonStudentProcessTest
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
    // TEST CASE: duplicates in ext. user store on UID
    // ILIOS USER ID: 4
    //
    // multiple records with the same UID
    array(
        'first_name' => 'Dave',
        'last_name' => 'Duplicate',
        'middle_name' => 'U',
        'email' => 'dave.u.duplicate@test.com',
        'phone' => '111-111-1114',
        'is_student' => false,
        'school_id' => 3,
        'graduation_year' => -1,
        'uid' => 'xxxx111114'
    ),
    array(
        'first_name' => 'David',
        'last_name' => 'Duplicate',
        'middle_name' => 'U',
        'email' => 'david.u.duplicate@test.com',
        'phone' => '111-111-1114',
        'is_student' => false,
        'school_id' => 3,
        'graduation_year' => -1,
        'uid' => 'xxxx111114'
    ),
    // TEST CASE: duplicates in ext. user store on email
    // ILIOS USER ID: 5
    //
    // multiple records with the same email
    array(
        'first_name' => 'Danielle',
        'last_name' => 'Duplicate',
        'middle_name' => 'E',
        'email' => 'danielle.e.duplicate@test.com',
        'phone' => '111-111-1115',
        'is_student' => false,
        'school_id' => 1,
        'graduation_year' => -1,
        'uid' => 'xxxx111115'
    ),
    array(
        'first_name' => 'Danielle',
        'last_name' => 'Duplicate',
        'middle_name' => 'E',
        'email' => 'danielle.e.duplicate@test.com',
        'phone' => '111-111-1115',
        'is_student' => false,
        'school_id' => 1,
        'graduation_year' => -1,
        'uid' => 'xxxx111115'
    ),
    // TEST CASE: Ilios user flagged to be ignored
    // ILIOS USER ID: 6
    array(
        'first_name' => 'STEVE',
        'last_name' => 'IGNORANT',
        'middle_name' => '',
        'email' => 'STEVE.IGNORANT@TEST.COM',
        'phone' => '111-111-1116',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => -1,
        'uid' => 'xxxx111116'
    ),
    // TEST CASE: Ilios user is disabled
    // ILIOS USER ID: 7
    array(
        'first_name' => 'DERRICK',
        'last_name' => 'DISABLED',
        'middle_name' => '',
        'email' => 'DERRICK.DISABLED@TEST.COM',
        'phone' => '111-111-1117',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => -1,
        'uid' => 'xxxx111117'
    ),
    // TEST CASE: uid match but email mismatch
    // ILIOS USER ID: 8
    array(
        'first_name' => 'Michael',
        'last_name' => 'Mismatch',
        'middle_name' => 'E',
        // email mismatch, the one on file in Ilios is 'michael.e.mismatch@test.com'
        'email' => 'michael.mismatch@test.com',
        'phone' => '111-111-1117',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => -1,
        'uid' => 'xxxx111118'
    ),
    // TEST CASE: email match but uid mismatch
    // ILIOS USER ID: 9
    array(
        'first_name' => 'Martha',
        'last_name' => 'Mismatch',
        'middle_name' => 'U',
        'email' => 'martha.u.mismatch@test.com',
        'phone' => '111-111-1119',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => -1,
        // UID mismatch, the one on file in Ilios is 'xxxx111119'
        'uid' => 'xxxx111199'
    ),
    // TEST CASE: duplicates on UID in Ilios
    // ILIOS USER ID: 10, 11
    array(
        'first_name' => 'David',
        'last_name' => 'Dupe',
        'middle_name' => 'E',
        'email' => 'david.e.dupe@test.com',
        'phone' => '555-555-5551',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => -1,
        'uid' => 'xxxx555551'
    ),
    // TEST CASE: duplicates on UID in Ilios
    // ILIOS USER ID: 12 - 14
    array(
        'first_name' => 'David',
        'last_name' => 'Doris',
        'middle_name' => 'U',
        'email' => 'doris.u.dupe@test.com',
        'phone' => '555-555-5555',
        'is_student' => false,
        'school_id' => 2,
        'graduation_year' => -1,
        'uid' => 'xxxx555555'
    ),

    // TEST CASE: successful user match/ update
    // ILIOS USER ID: 15
    //
    // matches on both email and UID
    // expected outcome: first/last/middle-name will be updated with the data below.
    array(
        'first_name' => 'GREG',
        'last_name' => 'GOODGUY',
        'middle_name' => '',
        'email' => 'greg.g.guy@test.com',
        'phone' => '999-999-9999',
        'is_student' => false,
        'school_id' => 3,
        'graduation_year' => -1,
        'uid' => 'xxxx999999'
    ),
);
