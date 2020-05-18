<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AssessmentOptionManager;

class AssessmentOption extends ReadWriteController
{
    public function __construct(AssessmentOptionManager $manager)
    {
        parent::__construct($manager, 'assessmentoptions');
    }
}
