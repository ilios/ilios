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
    public function setAamcCode(string $aamcCode): void;
    public function getAamcCode(): string;

    public function setAddressStreet(string $addressStreet): void;
    public function getAddressStreet(): string;

    public function setAddressCity(string $addressCity): void;
    public function getAddressCity(): string;

    public function setAddressStateOrProvince(string $addressStateOrProvince): void;
    public function getAddressStateOrProvince(): string;

    public function setAddressZipcode(string $addressZipcode): void;
    public function getAddressZipcode(): string;

    public function setAddressCountryCode(string $addressCountryCode): void;
    public function getAddressCountryCode(): string;
}
