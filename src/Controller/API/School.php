<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SchoolManager;

class School extends ReadWriteController
{
    public function __construct(SchoolManager $manager)
    {
        parent::__construct($manager, 'schools');
    }
}
