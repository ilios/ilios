<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CourseClerkshipTypeRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/courseclerkshiptypes")]
class CourseClerkshipTypes extends ReadWriteController
{
    public function __construct(CourseClerkshipTypeRepository $repository)
    {
        parent::__construct($repository, 'courseclerkshiptypes');
    }
}
