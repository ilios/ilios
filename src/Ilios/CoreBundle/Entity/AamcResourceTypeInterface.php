<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\CategorizableEntityInterface;
use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

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
