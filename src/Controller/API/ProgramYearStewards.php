<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ProgramYearStewardManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/programyearstewards")
 */
class ProgramYearStewards extends ReadWriteController
{
    public function __construct(ProgramYearStewardManager $manager)
    {
        parent::__construct($manager, 'programyearstewards');
    }
}
