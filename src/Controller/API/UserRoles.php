<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\UserRoleRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/userroles')]
class UserRoles extends ReadWriteController
{
    public function __construct(UserRoleRepository $repository)
    {
        parent::__construct($repository, 'userroles');
    }
}
