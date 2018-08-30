<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\StringableEntityInterface;

/**
 * Interface LoggableEntityInterface
 * Loggable entities have all changes logged automatically
 */
interface LoggableEntityInterface extends StringableEntityInterface
{
}
