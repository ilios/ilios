<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcResourceType;
use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Class LoadAamcResourceTypeData
 */
class LoadAamcResourceTypeData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('aamc_resource_type');
    }

    /**
     * @return AamcResourceTypeInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new AamcResourceType();
    }

    /**
     * @param AamcResourceTypeInterface $entity
     * @param array $data
     * @return IdentifiableEntityInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `resource_type_id`,`title`,`description`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        $entity->setDescription($data[2]);
        return $entity;
    }
}
