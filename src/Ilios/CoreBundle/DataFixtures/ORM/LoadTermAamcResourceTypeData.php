<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;
use Ilios\CoreBundle\Entity\Term;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class LoadSessionTypeAamcMethodData
 */
class LoadTermAamcResourceTypeData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('term_x_aamc_resource_type', false);
    }

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadTermData',
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcResourceTypeData',
        ];
    }

    /**
     * @return TermInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new Term();
    }

    /**
     * @param TermInterface $entity
     * @param array $data
     * @return TermInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `term_id`,`resource_type_id`
        // Ignore the given entity,
        // find the previously imported session type by its reference key instead.
        /* @var TermInterface $entity */
        $entity = $this->getReference('term' . $data[0]);
        /* @var AamcResourceTypeInterface $resourceType */
        $resourceType = $this->getReference('aamc_resource_type' . $data[1]);
        $entity->addAamcResourceType($resourceType);
        return $entity;
    }
}
