<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\MeshConceptRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/meshconcepts')]
class MeshConcepts extends ReadOnlyController
{
    public function __construct(MeshConceptRepository $repository)
    {
        parent::__construct($repository, 'meshconcepts');
    }
}
