<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AuthenticationManager;

class Authentication extends ReadWriteController
{
    public function __construct(AuthenticationManager $manager)
    {
        parent::__construct($manager, 'authentications');
    }
}
