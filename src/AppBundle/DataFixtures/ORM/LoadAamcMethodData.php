<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AamcMethod;
use AppBundle\Entity\AamcMethodInterface;
use AppBundle\Service\DataimportFileLocator;

/**
 * Class LoadAamcMethodData
 */
class LoadAamcMethodData extends AbstractFixture
{
    /**
     * LoadAamcMethodData constructor.
     * @param DataimportFileLocator $dataimportFileLocator
     */
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'aamc_method');
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
