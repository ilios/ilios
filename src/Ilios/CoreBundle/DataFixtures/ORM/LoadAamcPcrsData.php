<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcPcrs;

/**
 * Class LoadAamcPcrsData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadAamcPcrsData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('aamc_pcrs');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        $entity = new AamcPcrs();
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        return $entity;
    }
}
