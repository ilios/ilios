<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\SessionTypeInterface;

use AppBundle\Traits\DescribableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\SessionTypesEntityInterface;

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
