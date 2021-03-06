<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CompetencyRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v3>}/competencies")
 */
class Competencies extends ReadWriteController
{
    public function __construct(CompetencyRepository $repository)
    {
        parent::__construct($repository, 'competencies');
    }
}
