<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionDescriptionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/sessiondescriptions")
 */
class SessionDescriptions extends ReadWriteController
{
    public function __construct(SessionDescriptionManager $manager)
    {
        parent::__construct($manager, 'sessiondescriptions');
    }
}
