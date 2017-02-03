<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Interface SchoolConfigInterface
 * @package Ilios\CoreBundle\Entity
 */
interface SchoolConfigInterface extends SchoolEntityInterface, NameableEntityInterface, IdentifiableEntityInterface
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
