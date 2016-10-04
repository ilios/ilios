<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Class AlertableEntity
 * @package Ilios\CoreBundle\Traits
 */
trait AlertableEntity
{
    /**
     * @inheritdoc
     */
    public function setAlerts(Collection $alerts = null)
    {
        $this->alerts = new ArrayCollection();
        if (isNull($alerts)) {
            return;
        }

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    /**
     * @inheritdoc
     */
    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAlert(AlertInterface $alert)
    {
        $this->alerts->removeElement($alert);
    }

    /**
     * @inheritdoc
     */
    public function getAlerts()
    {
        return $this->alerts;
    }
}
