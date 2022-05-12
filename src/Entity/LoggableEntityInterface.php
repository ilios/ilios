<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\StringableEntityToIdInterface;

/**
 * Loggable entities have all changes logged automatically
 */
interface LoggableEntityInterface extends StringableEntityToIdInterface
{
}
