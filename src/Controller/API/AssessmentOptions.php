<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\AssessmentOptionManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v3>}/assessmentoptions")
 */
class AssessmentOptions extends ReadWriteController
{
    public function __construct(AssessmentOptionManager $manager)
    {
        parent::__construct($manager, 'assessmentoptions');
    }
}
