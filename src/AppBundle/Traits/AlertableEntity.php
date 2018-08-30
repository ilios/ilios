<?php

namespace AppBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use AppBundle\Entity\AlertInterface;

/**
 * Class AlertableEntity
 */
trait AlertableEntity
{
    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getAlerts()
    {
        return $this->alerts;
    }
}
