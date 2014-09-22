<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface SchoolInterface
 */
interface SchoolInterface 
{
    public function __toString();

    public function getSchoolId();

    public function setTemplatePrefix($templatePrefix);

    public function getTemplatePrefix();

    public function setTitle($title);

    public function getTitle();

    public function setIliosAdministratorEmail($iliosAdministratorEmail);

    public function getIliosAdministratorEmail();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setChangeAlertRecipients($changeAlertRecipients);

    public function getChangeAlertRecipients();

    public function addAlert(\Ilios\CoreBundle\Model\Alert $alerts);

    public function removeAlert(\Ilios\CoreBundle\Model\Alert $alerts);

    public function getAlerts();
}

