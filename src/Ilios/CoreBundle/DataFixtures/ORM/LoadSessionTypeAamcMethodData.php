<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\SessionType;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class LoadSessionTypeAamcMethodData
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
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcMethodData',
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSessionTypeData',
        ];
    }

    /**
     * @return SessionTypeInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new SessionType();
    }

    /**
     * @param SessionTypeInterface $entity
     * @param array $data
     * @return SessionTypeInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `session_type_id`,`method_id`
        /**
         * @var SessionTypeInterface $entity
         */
        // Ignore the given entity,
        // find the previously imported session type by its reference key instead.
        $entity = $this->getReference('session_type' . $data[0]);
        $entity->addAamcMethod($this->getReference('aamc_method' . $data[1]));
        return $entity;
    }
}
