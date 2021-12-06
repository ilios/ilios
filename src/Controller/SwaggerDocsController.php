<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SwaggerDocBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class SwaggerDocsController
 *
 * Produce the YAML files that document our endpoints
 *
 */
class SwaggerDocsController extends AbstractController
{
    public function __construct(protected SwaggerDocBuilder $builder, protected string $kernelProjectDir)
    {
    }

    /**
     * Get a single YAML file which documents our endpoints
     */
    public function indexAction(Request $request): Response
    {
        $yamlRoute = $this->generateUrl(
            'ilios_swagger_file',
            [],
            UrlGeneratorInterface::NETWORK_PATH
        );
        return $this->render('swagger/index.html.twig', ['yamlRoute' => $yamlRoute]);
    }

    /**
     * Fetch the swagger-ui from vendor and send its contents as the response
     */
    public function uiAction(Request $request, $fileName): Response
    {
        $fileName = empty($fileName) ? 'index.html' : $fileName;
        $swaggerDistDir = $this->kernelProjectDir . '/vendor/swagger-api/swagger-ui/dist';
        $filePath = "${swaggerDistDir}/${fileName}";

        if (!is_readable($filePath)) {
            throw new NotFoundHttpException("${fileName} can't be found");
        }

        $response = new BinaryFileResponse($filePath);
        $info = pathinfo($filePath);
        if ($info['extension'] === 'css') {
            $response->headers->set('Content-Type', 'text/css');
        }
        if ($info['extension'] === 'js') {
            $response->headers->set('Content-Type', 'text/javascript');
        }
        return $response;
    }

    /**
     * Get a single YAML file which documents our endpoints
     */
    public function yamlAction(Request $request): Response
    {
        $yaml = $this->builder->getDocs($request);

        return new Response(
            $yaml,
            Response::HTTP_OK,
            ['Content-type' => 'application/x-yaml']
        );
    }
}
