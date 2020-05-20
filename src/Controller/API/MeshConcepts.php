<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshConceptManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/meshconcepts")
 */
class MeshConcepts extends ReadOnlyController
{
    public function __construct(MeshConceptManager $manager)
    {
        parent::__construct($manager, 'meshconcepts');
    }
}
