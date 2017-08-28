<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\ApplicationConfig;
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;

/**
 * Class LoadApplicationConfigData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadApplicationConfigData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('application_config');
    }

    /**
     * @return ApplicationConfigInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new ApplicationConfig();
    }

    /**
     * @param ApplicationConfigInterface $entity
     * @param array $data
     * @return ApplicationConfigInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `id`, `name`,`value`
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $entity->setValue($data[2]);
        return $entity;
    }
}
