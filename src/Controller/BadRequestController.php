<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BadRequestController
 */
class BadRequestController extends AbstractController
{
    /**
     * This is the catch-all action for the api.
     * It sends a 404 and dies.
     */
    #[Route(
        '/api/{url}',
        requirements: [
            'url' => '(?!doc).+',
        ],
        defaults: [
            'url' => null,
        ],
        priority: -1,
    )]
    public function indexAction(): void
    {
        throw $this->createNotFoundException();
    }
}
