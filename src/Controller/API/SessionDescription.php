<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionDescriptionManager;

class SessionDescription extends ReadWriteController
{
    public function __construct(SessionDescriptionManager $manager)
    {
        parent::__construct($manager, 'sessiondescriptions');
    }
}
