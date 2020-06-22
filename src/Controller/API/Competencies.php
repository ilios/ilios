<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CompetencyManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/competencies")
 */
class Competencies extends ReadWriteController
{
    public function __construct(CompetencyManager $manager)
    {
        parent::__construct($manager, 'competencies');
    }
}
