<?php

namespace App\Entity;

use App\Traits\NameableEntityInterface;
use App\Traits\IdentifiableEntityInterface;


use App\Entity\SchoolInterface;
use App\Traits\SchoolEntityInterface;

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
