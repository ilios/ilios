<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CurriculumInventoryAcademicLevelRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/curriculuminventoryacademiclevels')]
class CurriculumInventoryAcademicLevels extends ReadWriteController
{
    public function __construct(CurriculumInventoryAcademicLevelRepository $repository)
    {
        parent::__construct($repository, 'curriculuminventoryacademiclevels');
    }
}
