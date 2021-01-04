<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\DepartmentRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1>}/departments")
 */
class Departments extends ReadOnlyController
{
    public function __construct(DepartmentRepository $repository)
    {
        parent::__construct($repository, 'departments');
    }
}
