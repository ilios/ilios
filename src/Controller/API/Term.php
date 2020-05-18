<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\TermManager;

class Term extends ReadWriteController
{
    public function __construct(TermManager $manager)
    {
        parent::__construct($manager, 'terms');
    }
}
