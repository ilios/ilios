<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\NameableEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\StringableEntityInterface;

/**
 * Interface SchoolConfigInterface
 */
interface SchoolConfigInterface extends
    SchoolEntityInterface,
    NameableEntityInterface,
    IdentifiableEntityInterface,
    StringableEntityInterface
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
