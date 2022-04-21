<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\AssessmentOptionRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/assessmentoptions")]
class AssessmentOptions extends ReadWriteController
{
    public function __construct(AssessmentOptionRepository $repository)
    {
        parent::__construct($repository, 'assessmentoptions');
    }
}
