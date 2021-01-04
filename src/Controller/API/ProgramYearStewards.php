<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\ProgramYearStewardRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1>}/programyearstewards")
 */
class ProgramYearStewards extends ReadOnlyController
{
    public function __construct(ProgramYearStewardRepository $repository)
    {
        parent::__construct($repository, 'programyearstewards');
    }
}
