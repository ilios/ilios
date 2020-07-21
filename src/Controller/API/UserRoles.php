<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\UserRoleManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/userroles")
 */
class UserRoles extends ReadWriteController
{
    public function __construct(UserRoleManager $manager)
    {
        parent::__construct($manager, 'userroles');
    }
}
