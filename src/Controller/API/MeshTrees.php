<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\MeshTreeRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/meshtrees')]
class MeshTrees extends ReadOnlyController
{
    public function __construct(MeshTreeRepository $repository)
    {
        parent::__construct($repository, 'meshtrees');
    }
}
