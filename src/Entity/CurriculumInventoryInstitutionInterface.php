<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\NameableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Entity\SchoolInterface;
use App\Traits\SchoolEntityInterface;

interface CurriculumInventoryInstitutionInterface extends
    NameableEntityInterface,
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface
{
    public function setAamcCode(string $aamcCode);
    public function getAamcCode(): string;

    public function setAddressStreet(string $addressStreet);
    public function getAddressStreet(): string;

    public function setAddressCity(string $addressCity);
    public function getAddressCity(): string;

    public function setAddressStateOrProvince(string $addressStateOrProvince);
    public function getAddressStateOrProvince(): string;

    public function setAddressZipcode(string $addressZipcode);
    public function getAddressZipcode(): string;

    public function setAddressCountryCode(string $addressCountryCode);
    public function getAddressCountryCode(): string;
}
