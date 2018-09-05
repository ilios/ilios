<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Manager\MeshDescriptorManager;
use AppBundle\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshConceptTermData
 */
class LoadMeshConceptTermData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_concept_x_term.csv',
            'MeshConceptTerm'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'AppBundle\DataFixtures\ORM\LoadMeshConceptData',
          'AppBundle\DataFixtures\ORM\LoadMeshTermData',
        ];
    }
}
