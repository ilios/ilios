<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshTreeManager;

class MeshTree extends ReadWriteController
{
    public function __construct(MeshTreeManager $manager)
    {
        parent::__construct($manager, 'meshtrees');
    }
}
