<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\LearningMaterialStatusRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/learningmaterialstatuses")]
class LearningMaterialStatuses extends ReadWriteController
{
    public function __construct(LearningMaterialStatusRepository $repository)
    {
        parent::__construct($repository, 'learningmaterialstatuses');
    }
}
