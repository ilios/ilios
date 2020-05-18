<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\InstructorGroupManager;

class InstructorGroup extends ReadWriteController
{
    public function __construct(InstructorGroupManager $manager)
    {
        parent::__construct($manager, 'instructorgroups');
    }
}
