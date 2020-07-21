<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshQualifierManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/meshqualifiers")
 */
class MeshQualifiers extends ReadOnlyController
{
    public function __construct(MeshQualifierManager $manager)
    {
        parent::__construct($manager, 'meshqualifiers');
    }
}
