<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CurriculumInventoryInstitutionManager;

class CurriculumInventoryInstitution extends ReadWriteController
{
    public function __construct(CurriculumInventoryInstitutionManager $manager)
    {
        parent::__construct($manager, 'curriculuminventoryinstitutions');
    }
}
