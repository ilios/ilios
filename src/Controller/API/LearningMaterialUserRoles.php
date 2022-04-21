<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\LearningMaterialUserRoleRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/learningmaterialuserroles")]
class LearningMaterialUserRoles extends ReadWriteController
{
    public function __construct(LearningMaterialUserRoleRepository $repository)
    {
        parent::__construct($repository, 'learningmaterialuserroles');
    }
}
