<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class CurriculumInventoryInstitution
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_institution")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
    use NameableEntity;
    use IdentifiableEntity;
    use StringableIdEntity;
    use SchoolEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="institution_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
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
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("aamcCode")
     */
    protected $aamcCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="address_street")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("addressStreet")
     */
    protected $addressStreet;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, name="address_city")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("addressCity")
     */
    protected $addressCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, name="address_state_or_province")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("addressStateOrProvince")
     */
    protected $addressStateOrProvince;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, name="address_zipcode")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("addressZipCode")
     */
    protected $addressZipCode;

    /**
     * @todo: get country list from SF service/convert to foreign key to a country table
     * @var string
     *
     * @ORM\Column(type="string", length=2, name="address_country_code")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 2
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("addressCountryCode")
     */
    protected $addressCountryCode;

    /**
     * @var SchoolInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\OneToOne(targetEntity="School", inversedBy="curriculumInventoryInstitution")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", unique=true, nullable=false)
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
     * @param string $addressZipCode
     */
    public function setAddressZipCode($addressZipCode)
    {
        $this->addressZipCode = $addressZipCode;
    }

    /**
     * @return string
     */
    public function getAddressZipCode()
    {
        return $this->addressZipCode;
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
}
