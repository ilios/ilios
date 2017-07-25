<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;

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
