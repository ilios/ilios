<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\IlmSessionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/ilmsessions")
 */
class IlmSessions extends ReadWriteController
{
    public function __construct(IlmSessionManager $manager)
    {
        parent::__construct($manager, 'ilmsessions');
    }
}
