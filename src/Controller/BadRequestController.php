<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BadRequestController
 */
class BadRequestController extends AbstractController
{
    /**
     * This is the catch-all action for the api.
     * It sends a 404 and dies.
     */
    public function indexAction()
    {
        throw $this->createNotFoundException();
    }
}
