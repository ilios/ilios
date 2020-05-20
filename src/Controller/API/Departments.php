<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\DepartmentManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/departments")
 */
class Departments extends ReadWriteController
{
    public function __construct(DepartmentManager $manager)
    {
        parent::__construct($manager, 'departments');
    }
}
