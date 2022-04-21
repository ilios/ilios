<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\IlmSessionRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/ilmsessions")]
class IlmSessions extends ReadWriteController
{
    public function __construct(IlmSessionRepository $repository)
    {
        parent::__construct($repository, 'ilmsessions');
    }
}
