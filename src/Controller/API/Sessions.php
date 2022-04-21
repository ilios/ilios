<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SessionRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/sessions")]
class Sessions extends ReadWriteController
{
    public function __construct(SessionRepository $repository)
    {
        parent::__construct($repository, 'sessions');
    }
}
