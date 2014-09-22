<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface AlertChangeTypeInterface
 */
interface AlertChangeTypeInterface 
{
    public function getAlertChangeTypeId();

    public function setTitle($title);

    public function getTitle();

    public function addAlert(\Ilios\CoreBundle\Model\Alert $alerts);

    public function removeAlert(\Ilios\CoreBundle\Model\Alert $alerts);

    public function getAlerts();
}
