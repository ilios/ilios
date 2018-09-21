<?php

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;

/**
 * Interface SchoolConfigInterface
 */
interface ApplicationConfigInterface extends IdentifiableEntityInterface, NameableEntityInterface
{
    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     */
    public function setValue($value);
}
