<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class CurriculumInventoryInstitution
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_institution")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
    use NameableEntity;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=100)
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="aamc_code", type="string", length=10)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $aamcCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="address_street")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="address_city")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, name="address_state_or_province")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, name="address_zipcode")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $zipCode;

    /**
     * @todo: get country list from SF service/convert to foreign key to a country table
     * @var string
     *
     * @ORM\Column(type="string", length=2, name="address_country_code")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $countryCode;

    /**
     * @var SchoolInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="School", inversedBy="curriculumInventoryInsitution")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $school;

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
        $this->id = $school->getId();
    }

    /**
     * @return SchoolInterface
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->school;
    }
}
