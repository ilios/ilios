<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CourseObjectiveRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller\API
 * @Route("/api/{version<v3>}/courseobjectives")
 */
class CourseObjectives extends ReadWriteController
{
    public function __construct(CourseObjectiveRepository $repository)
    {
        parent::__construct($repository, 'courseobjectives');
    }
}
