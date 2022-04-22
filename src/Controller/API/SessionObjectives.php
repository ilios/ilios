<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SessionObjectiveRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/sessionobjectives')]
class SessionObjectives extends ReadWriteController
{
    public function __construct(SessionObjectiveRepository $repository)
    {
        parent::__construct($repository, 'sessionobjectives');
    }
}
