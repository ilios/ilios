<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\VocabularyRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/vocabularies')]
class Vocabularies extends ReadWriteController
{
    public function __construct(VocabularyRepository $repository)
    {
        parent::__construct($repository, 'vocabularies');
    }
}
