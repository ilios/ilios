<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearningMaterialStatusManager;

class LearningMaterialStatus extends ReadWriteController
{
    public function __construct(LearningMaterialStatusManager $manager)
    {
        parent::__construct($manager, 'learningmaterialstatuses');
    }
}
