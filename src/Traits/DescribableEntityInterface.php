<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface DescribableEntityInterface
 */
interface DescribableEntityInterface
{
    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription(): ?string;
}
