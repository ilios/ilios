<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearningMaterialUserRoleManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/learningmaterialuserroles")
 */
class LearningMaterialUserRoles extends ReadWriteController
{
    public function __construct(LearningMaterialUserRoleManager $manager)
    {
        parent::__construct($manager, 'learningmaterialuserroles');
    }
}
