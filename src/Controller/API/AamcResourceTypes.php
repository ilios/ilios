<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\AamcResourceTypeRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/aamcresourcetypes')]
class AamcResourceTypes extends ReadWriteController
{
    public function __construct(AamcResourceTypeRepository $repository)
    {
        parent::__construct($repository, 'aamcresourcetypes');
    }
}
