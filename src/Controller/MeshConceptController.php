<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

/**
 * Class MeshConceptController
 * @package App\Controller
 */
class MeshConceptController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $this->fourTenAction();
    }

    /**
     * @inheritdoc
     */
    public function deleteAction($version, $object, $id)
    {
        $this->fourTenAction();
    }

    /**
     * Generic action used by the router to send a 410 GONE
     * to anyone trying to POST, PUT or DELETE a MeSH Concept
     * @throws GoneHttpException
     */
    public function fourTenAction()
    {
        throw new GoneHttpException('Creating, updating and deleting MeSH Concepts is no longer supported.');
    }
}
