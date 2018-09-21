<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\SessionTypeInterface;

use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SessionTypesEntityInterface;

/**
 * Interface AamcMethodInterface
 */
interface AamcMethodInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    LoggableEntityInterface,
    SessionTypesEntityInterface
{
}
