<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Traits\StringableEntityInterface;

/**
 * Interface LoggableEntityInterface
 * Loggable entities have all changes logged automatically
 */
interface LoggableEntityInterface extends StringableEntityInterface
{
}
