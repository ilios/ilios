<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearningMaterialUserRoleManager;

class LearningMaterialUserRole extends ReadWriteController
{
    public function __construct(LearningMaterialUserRoleManager $manager)
    {
        parent::__construct($manager, 'learningmaterialuserroles');
    }
}
