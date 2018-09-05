<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use AppBundle\Entity\AamcResourceTypeInterface;
use AppBundle\Entity\Term;
use AppBundle\Entity\TermInterface;

/**
 * Class LoadSessionTypeAamcMethodData
 */
class LoadTermAamcResourceTypeData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'term_x_aamc_resource_type', false);
    }

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadTermData',
            'AppBundle\DataFixtures\ORM\LoadAamcResourceTypeData',
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
