<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AamcResourceTypeManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/aamcresourcetypes")
 */
class AamcResourceTypes extends ReadWriteController
{
    public function __construct(AamcResourceTypeManager $manager)
    {
        parent::__construct($manager, 'aamcresourcetypes');
    }
}
