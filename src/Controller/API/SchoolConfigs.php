<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SchoolConfigRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/schoolconfigs')]
class SchoolConfigs extends ReadWriteController
{
    public function __construct(SchoolConfigRepository $repository)
    {
        parent::__construct($repository, 'schoolconfigs');
    }
}
