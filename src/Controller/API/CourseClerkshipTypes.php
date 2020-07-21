<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseClerkshipTypeManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/courseclerkshiptypes")
 */
class CourseClerkshipTypes extends ReadWriteController
{
    public function __construct(CourseClerkshipTypeManager $manager)
    {
        parent::__construct($manager, 'courseclerkshiptypes');
    }
}
