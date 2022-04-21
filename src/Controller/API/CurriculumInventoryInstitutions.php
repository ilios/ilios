<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CurriculumInventoryInstitutionRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/curriculuminventoryinstitutions")]
class CurriculumInventoryInstitutions extends ReadWriteController
{
    public function __construct(CurriculumInventoryInstitutionRepository $repository)
    {
        parent::__construct($repository, 'curriculuminventoryinstitutions');
    }
}
