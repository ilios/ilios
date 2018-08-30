<?php

namespace AppBundle\Entity;

use AppBundle\Traits\CategorizableEntityInterface;
use AppBundle\Traits\DescribableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\TitledEntityInterface;

/**
 * Interface AamcResourceTypeInterface
 */
interface AamcResourceTypeInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    StringableEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface
{
}
