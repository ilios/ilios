<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\AlertInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface AlertableEntityInterface
 */
interface AlertableEntityInterface
{
    public function setAlerts(?Collection $alerts = null): void;

    public function addAlert(AlertInterface $alert): void;

    public function removeAlert(AlertInterface $alert): void;

    public function getAlerts(): Collection;
}
