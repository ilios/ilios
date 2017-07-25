<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class IngestionExceptionController
 * IngestionExceptions can only be GETed nothing else
 */
class IngestionExceptionController extends ApiController
{
    /**
     * Send a 404 header to the user
     */
    public function fourOhFourAction()
    {
        throw new NotFoundHttpException('Curriculum Inventory Exports can only be created');
    }
}
