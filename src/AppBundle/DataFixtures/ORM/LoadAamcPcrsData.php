<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AamcPcrs;
use AppBundle\Entity\AamcPcrsInterface;
use AppBundle\Service\DataimportFileLocator;

/**
 * Class LoadAamcPcrsData
 */
class LoadAamcPcrsData extends AbstractFixture
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'aamc_pcrs');
    }

    /**
     * @return AamcPcrsInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new AamcPcrs();
    }

    /**
     * @param AamcPcrsInterface $entity
     * @param array $data
     * @return AamcPcrsInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `pcrs_id`,`description`
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        return $entity;
    }
}
