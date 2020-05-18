<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AamcPcrsManager;

class AamcPcrs extends ReadWriteController
{
    public function __construct(AamcPcrsManager $manager)
    {
        parent::__construct($manager, 'aamcpcrses');
    }
}
