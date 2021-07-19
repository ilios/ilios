<?php

declare(strict_types=1);

namespace App\Service\DataLoader;

use App\Repository\MeshDescriptorRepository;
use App\Service\DataimportFileLocator;

/**
 * Class LoadMeshDescriptorData
 */
class LoadMeshDescriptorData extends AbstractMeshFixture
{
    public function __construct(
        MeshDescriptorRepository $meshDescriptorRepository,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorRepository,
            $dataimportFileLocator,
            'mesh_descriptor.csv',
            'MeshDescriptor'
        );
    }
}
