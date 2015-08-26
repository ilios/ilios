<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcMethod;

/**
 * Class LoadAamcMethodData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadAamcMethodData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('aamc_method');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        $entity = new AamcMethod();
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        return $entity;
    }
}
