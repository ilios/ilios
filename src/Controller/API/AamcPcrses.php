<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AamcPcrsManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/aamcpcrses")
 */
class AamcPcrses extends ReadWriteController
{
    public function __construct(AamcPcrsManager $manager)
    {
        parent::__construct($manager, 'aamcpcrses');
    }
}
