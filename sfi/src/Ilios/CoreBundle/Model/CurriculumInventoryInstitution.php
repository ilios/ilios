<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurriculumInventoryInstitution
 */
class CurriculumInventoryInstitution
{
    /**
     * @var integer
     */
    private $schoolId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $aamcCode;

    /**
     * @var string
     */
    private $addressStreet;

    /**
     * @var string
     */
    private $addressCity;

    /**
     * @var string
     */
    private $addressStateOrProvince;

    /**
     * @var string
     */
    private $addressZipcode;

    /**
     * @var string
     */
    private $addressCountryCode;

    /**
     * @var \Ilios\CoreBundle\Entity\School
     */
    private $school;


    /**
     * Set schoolId
     *
     * @param integer $schoolId
     * @return CurriculumInventoryInstitution
     */
    public function setSchoolId($schoolId)
    {
        $this->schoolId = $schoolId;

        return $this;
    }

    /**
     * Get schoolId
     *
     * @return integer 
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CurriculumInventoryInstitution
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set aamcCode
     *
     * @param string $aamcCode
     * @return CurriculumInventoryInstitution
     */
    public function setAamcCode($aamcCode)
    {
        $this->aamcCode = $aamcCode;

        return $this;
    }

    /**
     * Get aamcCode
     *
     * @return string 
     */
    public function getAamcCode()
    {
        return $this->aamcCode;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     * @return CurriculumInventoryInstitution
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string 
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     * @return CurriculumInventoryInstitution
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string 
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set addressStateOrProvince
     *
     * @param string $addressStateOrProvince
     * @return CurriculumInventoryInstitution
     */
    public function setAddressStateOrProvince($addressStateOrProvince)
    {
        $this->addressStateOrProvince = $addressStateOrProvince;

        return $this;
    }

    /**
     * Get addressStateOrProvince
     *
     * @return string 
     */
    public function getAddressStateOrProvince()
    {
        return $this->addressStateOrProvince;
    }

    /**
     * Set addressZipcode
     *
     * @param string $addressZipcode
     * @return CurriculumInventoryInstitution
     */
    public function setAddressZipcode($addressZipcode)
    {
        $this->addressZipcode = $addressZipcode;

        return $this;
    }

    /**
     * Get addressZipcode
     *
     * @return string 
     */
    public function getAddressZipcode()
    {
        return $this->addressZipcode;
    }

    /**
     * Set addressCountryCode
     *
     * @param string $addressCountryCode
     * @return CurriculumInventoryInstitution
     */
    public function setAddressCountryCode($addressCountryCode)
    {
        $this->addressCountryCode = $addressCountryCode;

        return $this;
    }

    /**
     * Get addressCountryCode
     *
     * @return string 
     */
    public function getAddressCountryCode()
    {
        return $this->addressCountryCode;
    }

    /**
     * Set school
     *
     * @param \Ilios\CoreBundle\Entity\School $school
     * @return CurriculumInventoryInstitution
     */
    public function setSchool(\Ilios\CoreBundle\Entity\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \Ilios\CoreBundle\Entity\School 
     */
    public function getSchool()
    {
        return $this->school;
    }
}
