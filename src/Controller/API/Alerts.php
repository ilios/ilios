<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\AlertRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/alerts")]
class Alerts extends ReadWriteController
{
    public function __construct(AlertRepository $repository)
    {
        parent::__construct($repository, 'alerts');
    }
}
