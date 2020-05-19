<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshQualifierManager;

class MeshQualifier extends ReadOnlyController
{
    public function __construct(MeshQualifierManager $manager)
    {
        parent::__construct($manager, 'meshqualifiers');
    }
}
