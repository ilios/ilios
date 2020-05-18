<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ProgramYearManager;

class ProgramYear extends ReadWriteController
{
    public function __construct(ProgramYearManager $manager)
    {
        parent::__construct($manager, 'programyears');
    }
}
