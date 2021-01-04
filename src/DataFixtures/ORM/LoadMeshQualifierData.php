<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Repository\MeshDescriptorRepository;
use App\Service\DataimportFileLocator;

/**
 * Class LoadMeshQualifierData
 */
class LoadMeshQualifierData extends AbstractMeshFixture
{
    public function __construct(
        MeshDescriptorRepository $meshDescriptorRepository,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorRepository,
            $dataimportFileLocator,
            'mesh_qualifier.csv',
            'MeshQualifier'
        );
    }
}
