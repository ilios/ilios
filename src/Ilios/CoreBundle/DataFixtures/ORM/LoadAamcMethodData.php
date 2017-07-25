<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcMethod;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class LoadAamcMethodData
 */
class LoadAamcMethodData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('aamc_method');
    }

    /**
     * @return AamcMethodInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new AamcMethod();
    }

    /**
     * @param AamcMethodInterface $entity
     * @param array $data
     * @return AamcMethodInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `method_id`,`description`
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        return $entity;
    }
}
