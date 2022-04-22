<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CourseObjectiveRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/courseobjectives')]
class CourseObjectives extends ReadWriteController
{
    public function __construct(CourseObjectiveRepository $repository)
    {
        parent::__construct($repository, 'courseobjectives');
    }
}
