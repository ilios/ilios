<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearningMaterialStatusManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/learningmaterialstatuses")
 */
class LearningMaterialStatuses extends ReadWriteController
{
    public function __construct(LearningMaterialStatusManager $manager)
    {
        parent::__construct($manager, 'learningmaterialstatuses');
    }
}
