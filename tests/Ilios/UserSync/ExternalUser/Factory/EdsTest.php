<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestCase.php';

/**
 * Test Case for the EDS user factory.
 * @see Ilios_UserSync_ExternalUser_Factory_Eds
 */
class Ilios_UserSync_UserSource_Factory_EdsTest extends Ilios_TestCase
{

    /**
     * Data provider function for the graduation year extraction routine.
     * @return array
     */
    public function graduationYearProvider ()
    {
    	return array(
    		/* bad input */
    		array(null, -1),
    		array('', -1),
    		array(0, -1),
    		array('toolong', -1),
    		array('2$t', -1),

    		/* good input */
    		array('FA11', '2012'), // fall semester, expected graduation year bumped up by 1
    		array('FA12', '2013'),
    		array('FA13', '2014'),
    		array('FA14', '2015'),

    		array('SP11', '2011'),
    		array('SP12', '2012'),
    		array('SP13', '2013'),
    		array('SP14', '2014'),

    		array('ST11', '2011'),
    		array('ST12', '2012'),
    		array('ST13', '2013'),

    		array('WI11', '2011'),
    		array('WI12', '2012'),
    		array('WI13', '2013'),
	   	);
    }
    /**
     * Data provider function for the school mapping routine.
     * @return array
     */
    public function schoolIdProvider ()
    {
    	return array(
    		/* good input, 1-to-1 mapping of schools */
    		array(
    			Ilios_Config_Eds::SCHOOL_OF_DENTISTRY_ID,
    			Ilios_Config_Ucsf::SCHOOL_OF_DENTISTRY_ID
    		),
    		array(
    			Ilios_Config_Eds::SCHOOL_OF_MEDICINE_ID,
    			Ilios_Config_Ucsf::SCHOOL_OF_MEDICINE_ID
    		),
    		array(
    			Ilios_Config_Eds::SCHOOL_OF_NURSING_ID,
    			Ilios_Config_Ucsf::SCHOOL_OF_NURSING_ID
    		),
    		array(
    			Ilios_Config_Eds::SCHOOL_OF_PHARMACY_ID,
    			Ilios_Config_Ucsf::SCHOOL_OF_PHARMACY_ID
    		),

    		/* bad/missing input mapping, expected map to -1 */
    		array(
    			null,
    			-1
    		),
    		array(
    			'',
    			-1
    		),
    		array(
    			-1,
    			-1
    		),
    		array(
    			'some-other-nonsense',
    			-1
    		)
    	);
    }


    /**
     * Data provider function.
     * Returns a nested array of arrays, where in each sub-array
     * the first element represents user data as provided from
     * an LDAP result item as input to the factory,
     * and the second element representing the
     * expected external user as returned from the factory's createUser() method.
     * @return array
     */
    public function userProvider ()
    {
        $users = array(
        	array(
        		// empty input
        		array(),
        		new Ilios_UserSync_ExternalUser('', '', '', '', '', false, -1, -1, '')
        	),
        	array(
        		// full user data input
        		// student record
        		array(
        			'givenName' => array('Herp'),
        			'sn' => array('McDerp'),
        			'initials' => array('H'),
        			'mail' => array('Herp.McDerp@test.com'),
        			'ucsfEduStuSchoolCode' => array((string) Ilios_Config_Eds::SCHOOL_OF_MEDICINE_ID),
        			'ucsfEduIDNumber' => array('xxxx111111'),
        			'telephoneNumber' => array('111-111-1111'),
        			'eduPersonAffiliation' => array('count' => 2, 'something', 'student'),
					'ucsfEduStuGraduationTermExpected' => array('FA15'),
        		),
        		new Ilios_UserSync_ExternalUser('Herp', 'McDerp', 'H', 'Herp.McDerp@test.com',
        			'111-111-1111', true, Ilios_Config_Ucsf::SCHOOL_OF_MEDICINE_ID, 2016, 'xxxx111111')
        	),
        	array(
        		// non-student, no exp. graduation year
        		array(
        			'givenName' => array('Trevor'),
        			'sn' => array('Teacher'),
        			'initials' => array('G'),
        			'mail' => array('Trevor.Teacher@test.com'),
        			'ucsfEduStuSchoolCode' => array((string) Ilios_Config_Eds::SCHOOL_OF_PHARMACY_ID),
        			'ucsfEduIDNumber' => array('xxxx111112'),
        			'telephoneNumber' => array('111-111-1112'),
        			'eduPersonAffiliation' => array('count' => 2, 'something', 'else'),
        		),
        		new Ilios_UserSync_ExternalUser('Trevor', 'Teacher', 'G', 'Trevor.Teacher@test.com',
        			'111-111-1112', false, Ilios_Config_Ucsf::SCHOOL_OF_PHARMACY_ID, -1, 'xxxx111112')
        	)
        );

        return $users;
    }

    /**
     * @test
     * @covers Ilios_UserSync_ExternalUser_Factory_EdsTest::createUser
     * @dataProvider userProvider
     * @group ilios2
     * @group user_sync
     * @param array $userData nested array of user data as returned from EDS
     * @param Ilios_UserSync_ExternalUser $expectedUser expected external user object a generated from $edsData
     */
    public function testCreateUser (array $userData, Ilios_UserSync_ExternalUser $expectedUser)
    {
    	$factory = new Ilios_UserSync_ExternalUser_Factory_Eds();
        $actualUser = $factory->createUser($userData);
        // 1. check the type of the returned object
        $this->assertTrue($actualUser instanceof Ilios_UserSync_ExternalUser);
        $this->assertEquals($expectedUser, $actualUser);
    }

    /**
     * @test
     * @covers Ilios_UserSync_ExternalUser_Factory_EdsTest::determineGraduationYear
     * @dataProvider graduationYearProvider
     * @group ilios2
     * @group user_sync
     * @param string $text text containing the expected graduation year, as provided from EDS
     * @param int $expectedGraduationYear the expected corresponding graduation year as determined from the former input
     */
    public function testDetermineGraduationYear ($text, $expectedGraduationYear)
    {
    	$actualGraduationYear = Ilios_UserSync_ExternalUser_Factory_Eds::determineGraduationYear($text);
    	$this->assertEquals($expectedGraduationYear, $actualGraduationYear);
    }

    /**
     * @test
     * @covers Ilios_UserSync_ExternalUser_Factory_EdsTest::translateEdsSchoolCodeToIliosSchoolCode
     * @dataProvider schoolIdProvider
     * @group ilios2
     * @group user_sync
     * @param int $edsSchoolId the id/code of a UCSF school, as provided by EDS
     * @param int $expectedIliosSchoolId the expected Ilios-internal corresponding school id
     */
    public function translateEdsSchoolCodeToIliosSchoolCode ($edsSchoolId, $expectedIliosSchoolId)
    {
    	$actualIliosSchoolId = Ilios_UserSync_ExternalUser_Factory_Eds::translateEdsSchoolCodeToIliosSchoolCode($edsSchoolId);
    	$this->assertEquals($expectedIliosSchoolId, $actualIliosSchoolId);
    }
}
