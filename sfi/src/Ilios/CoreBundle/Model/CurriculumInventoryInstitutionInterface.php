<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface CurriculumInventoryInstitutionInterface
 */
interface CurriculumInventoryInstitutionInterface 
{
    public function setSchoolId($schoolId);

    public function getSchoolId();

    public function setName($name);

    public function getName();

    public function setAamcCode($aamcCode);

    public function getAamcCode();

    public function setAddressStreet($addressStreet);

    public function getAddressStreet();

    public function setAddressCity($addressCity);

    public function getAddressCity();

    public function setAddressStateOrProvince($addressStateOrProvince);

    public function getAddressStateOrProvince();

    public function setAddressZipcode($addressZipcode);

    public function getAddressZipcode();

    public function setAddressCountryCode($addressCountryCode);

    public function getAddressCountryCode();

    public function setSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getSchool();
}
