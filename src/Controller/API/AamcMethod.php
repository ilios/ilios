<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AamcMethodManager;

class AamcMethod extends ReadWriteController
{
    public function __construct(AamcMethodManager $manager)
    {
        parent::__construct($manager, 'aamcmethods');
    }
}
