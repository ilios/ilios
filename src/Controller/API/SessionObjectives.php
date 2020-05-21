<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionObjectiveManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/sessionobjectives")
 */
class SessionObjectives extends ReadWriteController
{
    public function __construct(SessionObjectiveManager $manager)
    {
        parent::__construct($manager, 'sessionobjectives');
    }
}
