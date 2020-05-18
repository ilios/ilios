<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ProgramManager;

class Program extends ReadWriteController
{
    public function __construct(ProgramManager $manager)
    {
        parent::__construct($manager, 'programs');
    }
}
