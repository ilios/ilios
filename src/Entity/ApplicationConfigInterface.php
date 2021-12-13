<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;

/**
 * Interface SchoolConfigInterface
 */
interface ApplicationConfigInterface extends IdentifiableEntityInterface, NameableEntityInterface
{
    public function getValue(): string;

    /**
     * @param string $value
     */
    public function setValue($value);
}
