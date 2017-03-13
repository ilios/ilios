<?php

namespace Ilios\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SwaggerDocsController
 *
 * Produce the YAML files that document our endpoints
 *
 * @package Ilios\ApiBundle\Controller
 */
class SwaggerDocsController extends Controller
{
    /**
     * Get a single YAML file which documents our endpoints
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $builder = $this->container->get('ilios_api.swagger_doc_builder');
        $yaml = $builder->getDocs($request);

        $response = new Response(
            $yaml,
            Response::HTTP_OK,
            ['Content-type' => 'application/x-yaml']
        );

        return $response;
    }
}
