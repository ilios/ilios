<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ProgramYearObjectiveManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller\API
 * @Route("/api/{version<v3>}/programyearobjectives")
 */
class ProgramYearObjectives extends ReadWriteController
{
    public function __construct(ProgramYearObjectiveManager $manager)
    {
        parent::__construct($manager, 'programyearobjectives');
    }
}
