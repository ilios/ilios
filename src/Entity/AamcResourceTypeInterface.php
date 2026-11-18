<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CategorizableEntityInterface;
use App\Entity\DescribableEntityInterface;
use App\Entity\IdentifiableStringEntityInterface;
use App\Entity\TitledEntityInterface;

interface AamcResourceTypeInterface extends
    IdentifiableStringEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface,
    DescribableEntityInterface
{
}
