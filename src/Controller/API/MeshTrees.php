<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshTreeManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/meshtrees")
 */
class MeshTrees extends ReadOnlyController
{
    public function __construct(MeshTreeManager $manager)
    {
        parent::__construct($manager, 'meshtrees');
    }
}
