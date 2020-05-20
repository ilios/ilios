<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionLearningMaterialManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/sessionlearningmaterials")
 */
class SessionLearningMaterials extends ReadWriteController
{
    public function __construct(SessionLearningMaterialManager $manager)
    {
        parent::__construct($manager, 'sessionlearningmaterials');
    }
}
