<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SchoolRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller\API
 * @Route("/api/{version<v1|v3>}/schools")
 */
class Schools extends ReadWriteController
{
    public function __construct(SchoolRepository $repository)
    {
        parent::__construct($repository, 'schools');
    }
}
