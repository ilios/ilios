<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\PendingUserUpdateManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/pendinguserupdates")
 */
class PendingUserUpdates extends ReadWriteController
{
    public function __construct(PendingUserUpdateManager $manager)
    {
        parent::__construct($manager, 'pendinguserupdates');
    }
}
