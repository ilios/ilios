<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ProgramYearObjectiveManager;

class ProgramYearObjective extends ReadWriteController
{
    public function __construct(ProgramYearObjectiveManager $manager)
    {
        parent::__construct($manager, 'programyearobjectives');
    }
}
