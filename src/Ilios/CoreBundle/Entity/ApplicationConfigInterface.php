<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;

/**
 * Interface SchoolConfigInterface
 * @package Ilios\CoreBundle\Entity
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
