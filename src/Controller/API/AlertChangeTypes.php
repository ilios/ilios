<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\AlertChangeTypeRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/alertchangetypes')]
class AlertChangeTypes extends ReadWriteController
{
    public function __construct(AlertChangeTypeRepository $repository)
    {
        parent::__construct($repository, 'alertchangetypes');
    }
}
