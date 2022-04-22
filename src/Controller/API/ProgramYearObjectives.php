<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\ProgramYearObjectiveRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/programyearobjectives')]
class ProgramYearObjectives extends ReadWriteController
{
    public function __construct(ProgramYearObjectiveRepository $repository)
    {
        parent::__construct($repository, 'programyearobjectives');
    }
}
