<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ProgramYearStewardManager;

class ProgramYearSteward extends ReadWriteController
{
    public function __construct(ProgramYearStewardManager $manager)
    {
        parent::__construct($manager, 'programyearstewards');
    }
}
