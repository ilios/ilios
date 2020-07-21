<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AlertChangeTypeManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/alertchangetypes")
 */
class AlertChangeTypes extends ReadWriteController
{
    public function __construct(AlertChangeTypeManager $manager)
    {
        parent::__construct($manager, 'alertchangetypes');
    }
}
