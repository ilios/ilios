<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshPreviousIndexingManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/meshpreviousindexings")
 */
class MeshPreviousIndexings extends ReadOnlyController
{
    public function __construct(MeshPreviousIndexingManager $manager)
    {
        parent::__construct($manager, 'meshpreviousindexings');
    }
}
