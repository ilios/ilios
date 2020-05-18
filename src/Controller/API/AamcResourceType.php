<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AamcResourceTypeManager;

class AamcResourceType extends ReadWriteController
{
    public function __construct(AamcResourceTypeManager $manager)
    {
        parent::__construct($manager, 'aamcresourcetypes');
    }
}
