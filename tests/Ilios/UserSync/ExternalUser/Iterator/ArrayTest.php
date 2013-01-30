<?php
require_once 'Ilios/TestCase.php';

/**
 * Test Case for the external user array iterator.
 *
 * @see Ilios_UserSync_ExternalUser_Iterator_Array
 */
class Ilios_UserSync_UserSource_Iterator_ArrayTest extends Ilios_TestCase
{

    /**
     * Data provider function.
     * Returns a nested array of arrays, where in each sub-array
     * * the first element is a nested array with each sub-array holding user attributes
     * @return array
     */
    public function provider ()
    {
        $users = array(
                    array(
                        array() // empty list
                    ),
                    array(
                        array( // list of two users
                            array( // fully populated student record
            					'first_name' => 'student',
                				'last_name' => 'a',
                				'middle_name' => '',
                				'email' => 'a.student@test.com',
                				'phone' => '111-111-1111',
                				'is_student' => true,
                				'school_id' => 2,
                				'graduation_year' => 2012,
                				'uid' => '1234567890'
                            ),
                            array( // non-student
            					'first_name' => 'foo',
                    			'last_name' => 'bar',
                    			'middle_name' => 'z',
                       			'email' => 'foo.bar@test.com',
                        		'phone' => '111-555-1111',
                		    	'is_student' => false,
                    			'school_id' => 7,
                    			'graduation_year' => 2022,
                    			'uid' => 'ucsf111111111'
                    		)
                        )
                    )
                );

        return $users;
    }

    /**
     * Tests the array iterator.
     * @test
     * @dataProvider provider
     * @group ilios
     * @group user_sync
     * @covers Ilios_UserSync_ExternalUser_Iterator_Array
     * @param array list of user records
     */
    public function testIterator ($users)
    {
        $factory = new Ilios_UserSync_ExternalUser_Factory_Array();
        $externalUsers = new Ilios_UserSync_ExternalUser_Iterator_Array($factory, $users);

        // check size of user list
        $this->assertEquals(count($users), count($externalUsers));

        // see if we can iterate and check data type
        $i = 0; // loop counter
        foreach ($externalUsers as $externalUser) {
            $this->assertTrue($externalUser instanceof Ilios_UserSync_ExternalUser);
            $i++;
        }

        // check if loop counter is correct so we know ALL records were processed
        $this->assertEquals(count($users), $i);

        // run through the loop again just to prove that iteration is repeatable
        $j = 0;
        foreach ($externalUsers as $externalUser) {
            $this->assertTrue($externalUser instanceof Ilios_UserSync_ExternalUser);
            $j++;
        }
        $this->assertEquals(count($users), $j);
    }
}
