<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Model\AlertInterface;
use Ilios\CoreBundle\Traits\IdentifiableTrait;

/**
 * Class AlertChangeType
 * @package Ilios\CoreBundle\Model
 */
class AlertChangeType implements AlertChangeTypeInterface
{
    use IdentifiableTrait;

    /**
     * @var string
     */
    private $title;

    /**
     * @var ArrayCollection
     */
    private $alerts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts)
    {
        $this->alerts = new ArrayCollection();

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert)
    {
        $this->alerts->add($alert);
    }

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts()
    {
        return $this->alerts;
    }
}
