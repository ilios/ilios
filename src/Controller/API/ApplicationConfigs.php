<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ApplicationConfigManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/applicationconfigs")
 */
class ApplicationConfigs extends ReadWriteController
{
    public function __construct(ApplicationConfigManager $manager)
    {
        parent::__construct($manager, 'applicationconfigs');
    }
}
