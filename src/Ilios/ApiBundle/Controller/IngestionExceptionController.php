<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class IngestionExceptionController
 * IngestionExceptions can only be GETed nothing else
 * @package Ilios\ApiBundle\Controller
 */
class IngestionExceptionController extends NonDtoApiController
{
    public function fourOhFourAction()
    {
        throw new NotFoundHttpException('Curriculum Inventory Exports can only be created');
    }
}
