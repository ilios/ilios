<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\AlertInterface;

/**
 * Interface AlertableEntityInterface
 */
interface AlertableEntityInterface
{
    public function setAlerts(Collection $alerts);

    public function addAlert(AlertInterface $alert);

    public function removeAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();
}
