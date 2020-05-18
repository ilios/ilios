<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionLearningMaterialManager;

class SessionLearningMaterial extends ReadWriteController
{
    public function __construct(SessionLearningMaterialManager $manager)
    {
        parent::__construct($manager, 'sessionlearningmaterials');
    }
}
