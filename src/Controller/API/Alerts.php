<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AlertManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/alerts")
 */
class Alerts extends ReadWriteController
{
    public function __construct(AlertManager $manager)
    {
        parent::__construct($manager, 'alerts');
    }
}
