<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\SessionDescriptionRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1>}/sessiondescriptions")
 * @deprecated
 */
class SessionDescriptions extends ReadOnlyController
{
    public function __construct(SessionDescriptionRepository $repository)
    {
        parent::__construct($repository, 'sessiondescriptions');
    }
}
