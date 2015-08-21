<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Interface LoggableEntityInterface
 * Loggable entities have all changes logged automatically
 * @package Ilios\CoreBundle\Entity
 */
interface LoggableEntityInterface extends StringableEntityInterface
{
}
