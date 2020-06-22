<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshTermManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/meshterms")
 */
class MeshTerms extends ReadOnlyController
{
    public function __construct(MeshTermManager $manager)
    {
        parent::__construct($manager, 'meshterms');
    }
}
