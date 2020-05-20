<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AamcMethodManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/aamcmethods")
 */
class AamcMethods extends ReadWriteController
{
    public function __construct(AamcMethodManager $manager)
    {
        parent::__construct($manager, 'aamcmethods');
    }
}
