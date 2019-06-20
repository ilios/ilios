<?php

namespace App\DataFixtures\ORM;

use App\Entity\AamcMethod;
use App\Entity\AamcMethodInterface;
use App\Service\DataimportFileLocator;

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
        // `method_id`,`description`,`active`
        $entity->setId($data[0]);
        $entity->setDescription($data[1]);
        $entity->setActive((boolean) $data[2]);
        return $entity;
    }
}
