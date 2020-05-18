<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CohortManager;

class Cohort extends ReadWriteController
{
    public function __construct(CohortManager $manager)
    {
        parent::__construct($manager, 'cohorts');
    }
}
