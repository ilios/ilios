<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SessionTypesEntityInterface;

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
