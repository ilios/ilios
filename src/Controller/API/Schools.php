<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SchoolRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/schools')]
class Schools extends ReadWriteController
{
    public function __construct(SchoolRepository $repository)
    {
        parent::__construct($repository, 'schools');
    }
}
