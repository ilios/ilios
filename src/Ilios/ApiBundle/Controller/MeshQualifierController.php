<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class MeshQualifierController
 * @package Ilios\ApiBundle\Controller
 */
class MeshQualifierController extends ApiController
{
    /**
     * Generic action used by the router to send a 410 GONE
     * to anyone trying to POST, PUT or DELETE a MeSH Qualifier
     */
    public function fourTenAction()
    {
        throw new GoneHttpException('Creating, updating and deleting MeSH Qualifiers is no longer supported.');
    }
}
