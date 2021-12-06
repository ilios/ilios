<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\SessionTypeInterface;

/**
 * Interface SessionTypesEntityInterface
 */
interface SessionTypesEntityInterface
{
    public function setSessionTypes(Collection $sessionTypes);

    public function addSessionType(SessionTypeInterface $sessionType);

    public function removeSessionType(SessionTypeInterface $sessionType);

    /**
    * @return SessionTypeInterface[]|ArrayCollection
    */
    public function getSessionTypes(): Collection;
}
