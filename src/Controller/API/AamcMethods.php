<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\AamcMethodRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/aamcmethods')]
class AamcMethods extends ReadWriteController
{
    public function __construct(AamcMethodRepository $repository)
    {
        parent::__construct($repository, 'aamcmethods');
    }
}
