<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseLearningMaterialManager;

class CourseLearningMaterial extends ReadWriteController
{
    public function __construct(CourseLearningMaterialManager $manager)
    {
        parent::__construct($manager, 'courselearningmaterials');
    }
}
