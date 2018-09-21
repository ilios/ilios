<?php

namespace App\DataFixtures\ORM;

use App\Entity\Manager\MeshDescriptorManager;
use App\Service\DataimportFileLocator;

/**
 * Class LoadMeshDescriptorData
 */
class LoadMeshDescriptorData extends AbstractMeshFixture
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_descriptor.csv',
            'MeshDescriptor'
        );
    }
}
