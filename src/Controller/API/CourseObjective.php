<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseObjectiveManager;

class CourseObjective extends ReadWriteController
{
    public function __construct(CourseObjectiveManager $manager)
    {
        parent::__construct($manager, 'courseobjectives');
    }
}
