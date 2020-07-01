<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseObjectiveManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller\API
 * @Route("/api/{version<v3>/courseobjectives"}
 */
class CourseObjectives extends ReadWriteController
{
    public function __construct(CourseObjectiveManager $manager)
    {
        parent::__construct($manager, 'courseobjectives');
    }
}
