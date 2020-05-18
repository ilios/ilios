<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\VocabularyManager;

class Vocabulary extends ReadWriteController
{
    public function __construct(VocabularyManager $manager)
    {
        parent::__construct($manager, 'vocabularies');
    }
}
