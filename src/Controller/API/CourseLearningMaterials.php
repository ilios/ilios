<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CourseLearningMaterialManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/courselearningmaterials")
 */
class CourseLearningMaterials extends ReadWriteController
{
    public function __construct(CourseLearningMaterialManager $manager)
    {
        parent::__construct($manager, 'courselearningmaterials');
    }
}
