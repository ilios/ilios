<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionTypeManager;

class SessionType extends ReadWriteController
{
    public function __construct(SessionTypeManager $manager)
    {
        parent::__construct($manager, 'sessiontypes');
    }
}
