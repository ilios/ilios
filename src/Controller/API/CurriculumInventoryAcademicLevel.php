<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventoryAcademicLevelManager;

class CurriculumInventoryAcademicLevel extends ReadWriteController
{
    public function __construct(CurriculumInventoryAcademicLevelManager $manager)
    {
        parent::__construct($manager, 'curriculuminventoryacademiclevels');
    }
}
