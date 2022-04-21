<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\TermRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/terms')]
class Terms extends ReadWriteController
{
    public function __construct(TermRepository $repository)
    {
        parent::__construct($repository, 'terms');
    }
}
