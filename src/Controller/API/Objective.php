<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ObjectiveManager;

class Objective extends ReadWriteController
{
    public function __construct(ObjectiveManager $manager)
    {
        parent::__construct($manager, 'objectives');
    }
}
