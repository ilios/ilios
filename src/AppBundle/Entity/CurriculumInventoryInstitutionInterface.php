<?php

namespace AppBundle\Entity;

use AppBundle\Traits\NameableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;


use AppBundle\Entity\SchoolInterface;
use AppBundle\Traits\SchoolEntityInterface;

/**
 * Interface CurriculumInventoryInstitutionInterface
 */
interface CurriculumInventoryInstitutionInterface extends
    NameableEntityInterface,
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param string $aamcCode
     */
    public function setAamcCode($aamcCode);

    /**
     * @return string
     */
    public function getAamcCode();

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet);

    /**
     * @return string
     */
    public function getAddressStreet();

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity);

    /**
     * @return string
     */
    public function getAddressCity();

    /**
     * @param string $addressStateOrProvince
     */
    public function setAddressStateOrProvince($addressStateOrProvince);

    /**
     * @return string
     */
    public function getAddressStateOrProvince();

    /**
     * @param string $addressZipcode
     */
    public function setAddressZipcode($addressZipcode);

    /**
     * @return string
     */
    public function getAddressZipcode();

    /**
     * @param string $addressCountryCode
     */
    public function setAddressCountryCode($addressCountryCode);

    /**
     * @return string
     */
    public function getAddressCountryCode();
}
