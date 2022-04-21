<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CurriculumInventorySequenceRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/curriculuminventorysequences")]
class CurriculumInventorySequences extends ReadWriteController
{
    public function __construct(CurriculumInventorySequenceRepository $repository)
    {
        parent::__construct($repository, 'curriculuminventorysequences');
    }
}
