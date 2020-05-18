<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseClerkshipTypeManager;

class CourseClerkshipType extends ReadWriteController
{
    public function __construct(CourseClerkshipTypeManager $manager)
    {
        parent::__construct($manager, 'courseclerkshiptypes');
    }
}
