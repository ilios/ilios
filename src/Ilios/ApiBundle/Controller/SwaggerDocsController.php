<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\ApiBundle\Service\SwaggerDocBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SwaggerDocsController
 *
 * Produce the YAML files that document our endpoints
 *
 * @package Ilios\ApiBundle\Controller
 */
class SwaggerDocsController extends AbstractController
{
    /**
     * @var SwaggerDocBuilder
     */
    protected $builder;

    /**
     * SwaggerDocsController constructor.
     * @param SwaggerDocBuilder $builder
     */
    public function __construct(SwaggerDocBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get a single YAML file which documents our endpoints
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $yaml = $this->builder->getDocs($request);

        $response = new Response(
            $yaml,
            Response::HTTP_OK,
            ['Content-type' => 'application/x-yaml']
        );

        return $response;
    }
}
