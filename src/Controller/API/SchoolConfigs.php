<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SchoolConfigManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/schoolconfigs")
 */
class SchoolConfigs extends ReadWriteController
{
    public function __construct(SchoolConfigManager $manager)
    {
        parent::__construct($manager, 'schoolconfigs');
    }
}
