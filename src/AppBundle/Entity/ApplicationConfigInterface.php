<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\NameableEntityInterface;

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
