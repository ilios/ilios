<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class SessionsEntity
 */
trait SessionConsolidationEntity
{
    public function getSessions(): Collection
    {
        $session = $this->getSession();
        return new ArrayCollection([$session]);
    }
}
