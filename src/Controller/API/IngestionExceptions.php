<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\IngestionExceptionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/ingestionexceptions")
 */
class IngestionExceptions extends ReadOnlyController
{
    public function __construct(IngestionExceptionManager $manager)
    {
        parent::__construct($manager, 'ingestionexceptions');
    }
}
