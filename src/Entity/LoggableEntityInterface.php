<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\StringableEntityToIdInterface;

/**
 * Loggable entities have all changes logged automatically
 */
interface LoggableEntityInterface extends StringableEntityToIdInterface
{
}
