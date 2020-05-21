<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\TermManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/terms")
 */
class Terms extends ReadWriteController
{
    public function __construct(TermManager $manager)
    {
        parent::__construct($manager, 'terms');
    }
}
