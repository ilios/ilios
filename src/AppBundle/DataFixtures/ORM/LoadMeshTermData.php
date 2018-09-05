<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Manager\MeshDescriptorManager;
use AppBundle\Service\DataimportFileLocator;

/**
 * Class LoadMeshTermData
 */
class LoadMeshTermData extends AbstractMeshFixture
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_term.csv',
            'MeshTerm'
        );
    }
}
