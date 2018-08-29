<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\SessionTypeInterface;

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
