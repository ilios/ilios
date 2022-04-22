<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\IngestionExceptionRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/ingestionexceptions')]
class IngestionExceptions extends ReadOnlyController
{
    public function __construct(IngestionExceptionRepository $repository)
    {
        parent::__construct($repository, 'ingestionexceptions');
    }
}
