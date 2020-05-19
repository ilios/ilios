<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\IngestionExceptionManager;

class IngestionException extends ReadOnlyController
{
    public function __construct(IngestionExceptionManager $manager)
    {
        parent::__construct($manager, 'ingestionexceptions');
    }
}
