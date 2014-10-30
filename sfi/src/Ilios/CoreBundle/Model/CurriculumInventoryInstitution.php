<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;

use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * Class CurriculumInventoryInstitution
 * @package Ilios\CoreBundle\Model
 */
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
    use IdentifiableEntity;
    use NameableEntity;

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
     * @var SchoolInterface
     */
    private $school;

    /**
     * @param string $aamcCode
     */
    public function setAamcCode($aamcCode)
    {
        $this->aamcCode = $aamcCode;
    }

    /**
     * @return string
     */
    public function getAamcCode()
    {
        return $this->aamcCode;
    }

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    /**
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * @return string
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * @param string $addressStateOrProvince
     */
    public function setAddressStateOrProvince($addressStateOrProvince)
    {
        $this->addressStateOrProvince = $addressStateOrProvince;
    }

    /**
     * @return string
     */
    public function getAddressStateOrProvince()
    {
        return $this->addressStateOrProvince;
    }

    /**
     * @param string $addressZipcode
     */
    public function setAddressZipcode($addressZipcode)
    {
        $this->addressZipcode = $addressZipcode;
    }

    /**
     * @return string
     */
    public function getAddressZipcode()
    {
        return $this->addressZipcode;
    }

    /**
     * @param string $addressCountryCode
     */
    public function setAddressCountryCode($addressCountryCode)
    {
        $this->addressCountryCode = $addressCountryCode;
    }

    /**
     * @return string
     */
    public function getAddressCountryCode()
    {
        return $this->addressCountryCode;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getSchool()
    {
        return $this->school;
    }
}
