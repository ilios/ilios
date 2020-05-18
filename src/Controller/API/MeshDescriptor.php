<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshDescriptorManager;

class MeshDescriptor extends ReadWriteController
{
    public function __construct(MeshDescriptorManager $manager)
    {
        parent::__construct($manager, 'meshdescriptors');
    }
}
