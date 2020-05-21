<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ObjectiveManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/objectives")
 */
class Objectives extends ReadWriteController
{
    public function __construct(ObjectiveManager $manager)
    {
        parent::__construct($manager, 'objectives');
    }
}
