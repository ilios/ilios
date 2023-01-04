<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait AlertableEntity
{
    protected Collection $alerts;

    public function setAlerts(Collection $alerts = null)
    {
        $this->alerts = new ArrayCollection();
        if (is_null($alerts)) {
            return;
        }

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    public function getAlerts(): Collection
    {
        return $this->alerts;
    }
}
