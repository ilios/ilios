<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CourseLearningMaterialRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/courselearningmaterials')]
class CourseLearningMaterials extends ReadWriteController
{
    public function __construct(CourseLearningMaterialRepository $repository)
    {
        parent::__construct($repository, 'courselearningmaterials');
    }
}
