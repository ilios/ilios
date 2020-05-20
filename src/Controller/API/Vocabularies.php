<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\VocabularyManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/vocabularies")
 */
class Vocabularies extends ReadWriteController
{
    public function __construct(VocabularyManager $manager)
    {
        parent::__construct($manager, 'vocabularies');
    }
}
