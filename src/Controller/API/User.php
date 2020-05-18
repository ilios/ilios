<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\UserManager;

class User extends ReadWriteController
{
    public function __construct(UserManager $manager)
    {
        parent::__construct($manager, 'users');
    }
}
