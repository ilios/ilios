<?php

namespace App\DataFixtures\ORM;

use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use App\Entity\SessionType;
use App\Entity\SessionTypeInterface;

/**
 * Class LoadSessionTypeAamcMethodData
 */
class LoadSessionTypeAamcMethodData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'session_type_x_aamc_method', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadAamcMethodData',
            'AppBundle\DataFixtures\ORM\LoadSessionTypeData',
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
