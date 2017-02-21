<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
