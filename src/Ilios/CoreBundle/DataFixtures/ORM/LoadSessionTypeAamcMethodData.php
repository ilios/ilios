<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\SessionType;

/**
 * Class LoadSessionTypeAamcMethodData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadSessionTypeAamcMethodData extends AbstractFixture implements DependentFixtureInterface

{
    public function __construct()
    {
        parent::__construct('session_type_x_aamc_method', false);
    }
    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `session_type_id`,`method_id`
        /**
         * @var SessionType $entity
         */
        $entity = $this->getReference('session_type' . $data[0]);
        $entity->addAamcMethod($this->getReference('aamc_method' . $data[1]));
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcMethodData',
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSessionTypeData',
        ];
    }
}