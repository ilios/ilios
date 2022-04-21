<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\MeshTermRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/meshterms')]
class MeshTerms extends ReadOnlyController
{
    public function __construct(MeshTermRepository $repository)
    {
        parent::__construct($repository, 'meshterms');
    }
}
