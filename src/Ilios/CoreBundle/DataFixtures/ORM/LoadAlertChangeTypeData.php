<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AlertChangeType;

/**
 * Class LoadAlertChangeTypeData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadAlertChangeTypeData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('alert_change_type');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `alert_change_type_id`,`title`
        $entity = new AlertChangeType();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
