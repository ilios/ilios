<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\OfferingManager;

class Offering extends ReadWriteController
{
    public function __construct(OfferingManager $manager)
    {
        parent::__construct($manager, 'offerings');
    }
}
