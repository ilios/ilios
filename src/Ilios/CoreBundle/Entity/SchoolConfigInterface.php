<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

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
