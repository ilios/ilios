<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;

use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * Class CurriculumInventoryInstitution
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="curriculum_inventory_institution")
 */
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
//    use IdentifiableEntity;
    use NameableEntity;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $aamcCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="address_street")
     */
    protected $streetAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="address_city")
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="state_or_province")
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, name="address_zipcode")
     */
    protected $zipCode;

    /**
     * @todo: get country list from SF service/convert to foreign key to a country table
     * @var string
     *
     * @ORM\Column(type="string", length=2, name="address_country_code")
     */
    protected $countryCode;

    /**
     * @todo Create a proper ID column in the DB. There's currently no uniqueness enforced...
     * @var SchoolInterface
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="School", inversedBy="curriculumInventoryInsitutions")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     */
    protected $school;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        throw new \LogicException('This method should not be called until table has proper primary key.');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->getSchool()->getId() : $this->id;
    }

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
}
