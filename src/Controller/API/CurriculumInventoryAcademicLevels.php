<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventoryAcademicLevelManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/curriculuminventoryacademiclevels")
 */
class CurriculumInventoryAcademicLevels extends ReadWriteController
{
    public function __construct(CurriculumInventoryAcademicLevelManager $manager)
    {
        parent::__construct($manager, 'curriculuminventoryacademiclevels');
    }
}
