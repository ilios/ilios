<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionDescriptionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1>}/sessiondescriptions")
 * @deprecated
 */
class SessionDescriptions extends ReadOnlyController
{
    public function __construct(SessionDescriptionManager $manager)
    {
        parent::__construct($manager, 'sessiondescriptions');
    }
}
