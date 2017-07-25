<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AlertChangeType;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class LoadAlertChangeTypeData
 */
class LoadAlertChangeTypeData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('alert_change_type');
    }

    /**
     * @return AlertChangeTypeInterface
     *
     * @see AbstractFixture::createEntity()
     */
    public function createEntity()
    {
        return new AlertChangeType();
    }

    /**
     * @param AlertChangeTypeInterface $entity
     * @param array $data
     * @return AlertChangeTypeInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `alert_change_type_id`,`title`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
