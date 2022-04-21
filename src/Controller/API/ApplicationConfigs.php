<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\ApplicationConfigRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/applicationconfigs")]
class ApplicationConfigs extends ReadWriteController
{
    public function __construct(ApplicationConfigRepository $repository)
    {
        parent::__construct($repository, 'applicationconfigs');
    }
}
