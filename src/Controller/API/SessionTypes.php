<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SessionTypeRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/sessiontypes")]
class SessionTypes extends ReadWriteController
{
    public function __construct(SessionTypeRepository $repository)
    {
        parent::__construct($repository, 'sessiontypes');
    }
}
