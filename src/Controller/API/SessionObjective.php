<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionObjectiveManager;

class SessionObjective extends ReadWriteController
{
    public function __construct(SessionObjectiveManager $manager)
    {
        parent::__construct($manager, 'sessionobjectives');
    }
}
