<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\IlmSessionManager;

class IlmSession extends ReadWriteController
{
    public function __construct(IlmSessionManager $manager)
    {
        parent::__construct($manager, 'ilmsessions');
    }
}
