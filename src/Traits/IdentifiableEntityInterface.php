<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Interface IdentifiableTraitIntertface
 */
interface IdentifiableEntityInterface
{
    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId(): mixed;
}
