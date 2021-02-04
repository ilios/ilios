<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\MeshConceptRepository;
use App\Traits\APIController\GetAll;
use App\Traits\APIController\GetOne;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v3>}/meshconcepts")
 */
class MeshConcepts
{
    use GetOne;
    use GetAll;

    protected MeshConceptRepository $repository;

    public function __construct(MeshConceptRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getRepository(): MeshConceptRepository
    {
        return $this->repository;
    }

    protected function getEndpoint(): string
    {
        return 'meshconcepts';
    }
}
