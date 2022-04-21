<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\AamcPcrsRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/{version<v3>}/aamcpcrses")]
class AamcPcrses extends ReadWriteController
{
    public function __construct(AamcPcrsRepository $repository)
    {
        parent::__construct($repository, 'aamcpcrses');
    }
}
