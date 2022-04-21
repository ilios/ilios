<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SessionLearningMaterialRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/sessionlearningmaterials")]
class SessionLearningMaterials extends ReadWriteController
{
    public function __construct(SessionLearningMaterialRepository $repository)
    {
        parent::__construct($repository, 'sessionlearningmaterials');
    }
}
