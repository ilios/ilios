<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\PendingUserUpdateRepository;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/{version<v3>}/pendinguserupdates')]
class PendingUserUpdates extends ReadWriteController
{
    public function __construct(PendingUserUpdateRepository $repository)
    {
        parent::__construct($repository, 'pendinguserupdates');
    }
}
