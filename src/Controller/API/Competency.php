<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CompetencyManager;

class Competency extends ReadWriteController
{
    public function __construct(CompetencyManager $manager)
    {
        parent::__construct($manager, 'competencies');
    }
}
