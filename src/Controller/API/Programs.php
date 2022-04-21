<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\ProgramRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/programs')]
class Programs extends ReadWriteController
{
    public function __construct(ProgramRepository $repository)
    {
        parent::__construct($repository, 'programs');
    }
}
