<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionObjectiveManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller\API
 * @Route("/api/{version<v3>/sessionobjectives"}
 */
class SessionObjectives extends ReadWriteController
{
    public function __construct(SessionObjectiveManager $manager)
    {
        parent::__construct($manager, 'sessionobjectives');
    }
}
