<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class MeshConceptController
 * @package Ilios\ApiBundle\Controller
 */
class MeshConceptController extends ApiController
{
    /**
     * Generic action used by the router to send a 410 GONE
     * to anyone trying to POST, PUT or DELETE a MeSH Concept
     */
    public function fourTenAction()
    {
        throw new GoneHttpException('Creating, updating and deleting MeSH Concepts is no longer supported.');
    }
}
