<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\DepartmentManager;

class Department extends ReadWriteController
{
    public function __construct(DepartmentManager $manager)
    {
        parent::__construct($manager, 'departments');
    }
}
