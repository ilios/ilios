<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Interface AlertableEntityInterface
 */
interface AlertableEntityInterface
{
    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts);

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert);

    /**
     * @param AlertInterface $alert
     */
    public function removeAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();
}
