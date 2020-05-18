<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseManager;

class Course extends ReadWriteController
{
    public function __construct(CourseManager $manager)
    {
        parent::__construct($manager, 'courses');
    }
}
