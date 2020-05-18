<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearningMaterialManager;

class LearningMaterial extends ReadWriteController
{
    public function __construct(LearningMaterialManager $manager)
    {
        parent::__construct($manager, 'learningmaterials');
    }
}
