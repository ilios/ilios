<?php

declare(strict_types=1);

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

    public function getAamcCode(): string;

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet);

    public function getAddressStreet(): string;

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity);

    public function getAddressCity(): string;

    /**
     * @param string $addressStateOrProvince
     */
    public function setAddressStateOrProvince($addressStateOrProvince);

    public function getAddressStateOrProvince(): string;

    /**
     * @param string $addressZipcode
     */
    public function setAddressZipcode($addressZipcode);

    public function getAddressZipcode(): string;

    /**
     * @param string $addressCountryCode
     */
    public function setAddressCountryCode($addressCountryCode);

    public function getAddressCountryCode(): string;
}
