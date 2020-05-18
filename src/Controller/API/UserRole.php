<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\UserRoleManager;

class UserRole extends ReadWriteController
{
    public function __construct(UserRoleManager $manager)
    {
        parent::__construct($manager, 'userroles');
    }
}
