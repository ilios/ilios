<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ActivatableEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SessionTypesEntityInterface;

/**
 * Interface AamcMethodInterface
 */
interface AamcMethodInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    SessionTypesEntityInterface,
    ActivatableEntityInterface
{
    /**
     * @param string $description
     */
    public function setDescription($description);

    public function getDescription(): string;
}
