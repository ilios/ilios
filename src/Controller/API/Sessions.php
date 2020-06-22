<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/sessions")
 */
class Sessions extends ReadWriteController
{
    public function __construct(SessionManager $manager)
    {
        parent::__construct($manager, 'sessions');
    }
}
