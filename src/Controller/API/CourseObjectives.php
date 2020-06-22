<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseObjectiveManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/courseobjectives")
 */
class CourseObjectives extends ReadWriteController
{
    public function __construct(CourseObjectiveManager $manager)
    {
        parent::__construct($manager, 'courseobjectives');
    }
}
