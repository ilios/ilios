<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Model\AlertInterface;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;

/**
 * Interface AlertChangeTypeInterface
 * @package Ilios\CoreBundle\Model
 */
interface AlertChangeTypeInterface extends IdentifiableTraitIntertface
{
    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts);

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();
}

