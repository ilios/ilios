<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\MeshQualifierRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/meshqualifiers")]
class MeshQualifiers extends ReadOnlyController
{
    public function __construct(MeshQualifierRepository $repository)
    {
        parent::__construct($repository, 'meshqualifiers');
    }
}
