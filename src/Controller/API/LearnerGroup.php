<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\LearnerGroupManager;

class LearnerGroup extends ReadWriteController
{
    public function __construct(LearnerGroupManager $manager)
    {
        parent::__construct($manager, 'learnergroups');
    }
}
