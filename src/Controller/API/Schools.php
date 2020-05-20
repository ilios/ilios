<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SchoolManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/schools")
 */
class Schools extends ReadWriteController
{
    public function __construct(SchoolManager $manager)
    {
        parent::__construct($manager, 'schools');
    }
}
