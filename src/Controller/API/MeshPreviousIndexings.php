<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\MeshPreviousIndexingRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/meshpreviousindexings')]
class MeshPreviousIndexings extends ReadOnlyController
{
    public function __construct(MeshPreviousIndexingRepository $repository)
    {
        parent::__construct($repository, 'meshpreviousindexings');
    }
}
