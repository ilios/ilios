<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\School;

/**
 * Class LoadSchoolData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadSchoolData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('school');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `school_id`,`template_prefix`,`title`,`ilios_administrator_email`,`deleted`,`change_alert_recipients`
        $entity = new School();
        $entity->setId($data[0]);
        $entity->setTemplatePrefix($data[1]);
        $entity->setTitle($data[2]);
        $entity->setIliosAdministratorEmail($data[3]);
        $entity->setDeleted((boolean) $data[4]);
        $entity->setChangeAlertRecipients($data[5]);
        return $entity;
    }
}
