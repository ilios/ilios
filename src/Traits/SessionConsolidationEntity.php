<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\SessionInterface;

/**
 * Class SessionsEntity
 */
trait SessionConsolidationEntity
{
    public function getSessions(): Collection
    {
        $session = $this->getSession();
        if ($session) {
            return new ArrayCollection([$session]);
        }

        return new ArrayCollection();
    }
}
