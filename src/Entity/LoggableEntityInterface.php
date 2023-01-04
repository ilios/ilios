<?php

declare(strict_types=1);

namespace App\Entity;

use Stringable;

/**
 * Loggable entities have all changes logged automatically
 */
interface LoggableEntityInterface extends Stringable
{
}
