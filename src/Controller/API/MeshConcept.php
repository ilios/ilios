<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshConceptManager;

class MeshConcept extends ReadOnlyController
{
    public function __construct(MeshConceptManager $manager)
    {
        parent::__construct($manager, 'meshconcepts');
    }
}
