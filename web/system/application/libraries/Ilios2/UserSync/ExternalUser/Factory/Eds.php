<?php

/**
 * Factory class for creating 'external user' objects,
 * based on search results returned from the UCSF Enterprise Directory Service (EDS).
 *
 * Use it in combination with the corresponding EDS user source class and the
 * LDAP external user iterator.
 *
 * @see Ilios2_UserSync_ExternalUser_Iterator_Ldap
 * @see Ilios2_UserSync_UserSource_Eds
 */
class Ilios2_UserSync_ExternalUser_Factory_Eds implements Ilios2_UserSync_ExternalUser_Factory
{
	/**
	 * Creates an external user object from a given nested array of LDAP search result entry attributes.
	 * @param array $properties LDAP entry attributes
	 * @return Ilios2_UserSync_ExternalUser
     * @see Ilios2_UserSync_ExternalUser_Factory::createUser()
     */
    public function createUser (array $properties)
    {
        // As stated in the class doc-block,
        // we are dealing with LDAP entries here.
        // Case matters!

        // extract the user props from the given array
        $firstName = '';
        if (array_key_exists('givenName', $properties)) {
            $firstName =  $properties['givenName'][0];
        }

        $lastName = '';
        if (array_key_exists('sn', $properties)) {
            $lastName =  $properties['sn'][0];
        }

        $middleName = ''; // its just the initial, actually
        if (array_key_exists('initials', $properties)) {
            $middleName = $properties['initials'][0];
        }

        $email = '';
        if (array_key_exists('mail', $properties)) {
            $email = $properties['mail'][0];
        }

        $edsSchoolId = -1;
        if (array_key_exists('ucsfEduStuSchoolCode', $properties)) {
            $edsSchoolId = (int) $properties['ucsfEduStuSchoolCode'][0];
        }

        $iliosSchoolId = self::translateEdsSchoolCodeToIliosSchoolCode($edsSchoolId);

        $uid = '';
        if (array_key_exists('ucsfEduIDNumber', $properties)) {
            $uid = $properties['ucsfEduIDNumber'][0];
        }

        $phone = '';
        if (array_key_exists('telephoneNumber', $properties)) {
            $phone = $properties['telephoneNumber'][0];
        }  elseif (array_key_exists('mobile', $properties)) {
            $phone = $properties['mobile'][0];
        } elseif (array_key_exists('ucsfEduSecondaryTelephoneNumber', $properties)) {
            $phone = $properties['ucsfEduSecondaryTelephoneNumber'][0];
        }

        $isStudent = false;
        if (array_key_exists('eduPersonAffiliation', $properties)) {
            $affiliations = $properties['eduPersonAffiliation'];
            for ($i = 0, $n = (int) $affiliations['count']; $i < $n; $i++) {
                if ('student' == $affiliations[$i]) {
                    $isStudent = true;
                    break;
                }
            }
        }

        $graduationYear = -1;

        if (array_key_exists('ucsfEduStuGraduationTermExpected', $properties)) {
            $graduationYear = self::determineGraduationYear($properties['ucsfEduStuGraduationTermExpected'][0]);
        }

        // at last, create the user object and return it
        return new Ilios2_UserSync_ExternalUser($firstName, $lastName, $middleName, $email, $phone, $isStudent, $iliosSchoolId, $graduationYear, $uid);
    }

    /**
     * Attempts to extract the student's expected graduation year
     * from a given text.
     * @param string $text a text string containing the expected graduation year and -semester.
     * @return int the 4-digit graduation year, if none could be determined -1 is returned
     */
    public static function determineGraduationYear ($text)
    {
        $graduationYear = -1;

        // catch missing/bad input
        if (empty($text) || 4 != strlen($text)) {
            return $graduationYear;
        }

        // slice and dice the year out of the given text
        $graduationYear = 2000 + intval(substr($text, 2, 2), 10);
        if (substr($text, 0, 2) == 'FA') {
            $graduationYear++;
        }
        return $graduationYear;
    }

    /**
     * Maps the given school code from Eds to its Ilios-internal equivalent.
     * @param int $edsSchoolCode the school code as provided by EDS
     * @return int the corresponding Ilios-internal school id, or -1 if no mapping could be achieved
     * @see Ilios2_Config_Eds
     * @see Ilios2_Config_Ucsf
     */
    public static function translateEdsSchoolCodeToIliosSchoolCode ($edsSchoolCode)
    {
        $iliosSchoolCode = -1;

        switch ($edsSchoolCode) { // school mapping
            case Ilios2_Config_Eds::SCHOOL_OF_DENTISTRY_ID :
            	$iliosSchoolCode = Ilios2_Config_Ucsf::SCHOOL_OF_DENTISTRY_ID;
            	break;
            case Ilios2_Config_Eds::SCHOOL_OF_MEDICINE_ID :
            	$iliosSchoolCode = Ilios2_Config_Ucsf::SCHOOL_OF_MEDICINE_ID;
            	break;
            case Ilios2_Config_Eds::SCHOOL_OF_PHARMACY_ID :
            	$iliosSchoolCode = Ilios2_Config_Ucsf::SCHOOL_OF_PHARMACY_ID;
            	break;
            case Ilios2_Config_Eds::SCHOOL_OF_NURSING_ID :
            	$iliosSchoolCode = Ilios2_Config_Ucsf::SCHOOL_OF_NURSING_ID;
            	break;
            default :
                // do nothing
        }
        return $iliosSchoolCode;
    }
}
