<?php

namespace App\Entity;

use App\Traits\CategorizableEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\TitledEntityInterface;

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
