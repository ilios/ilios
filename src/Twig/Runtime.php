<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\RuntimeExtensionInterface;

class Runtime implements RuntimeExtensionInterface
{
    public function __construct(
        protected Environment $twig,
        protected RouterInterface $router,
        protected string $environment,
        protected string $apiVersion
    ) {
    }

    /**
     * KLUDGE!
     * Twig filter that injects our descriptions and version into the API docs data structure before it gets rendered.
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function insertApiDocInfo(array $swaggerData): array
    {
        $apiDocsUrl = $this->router->generate(
            'app.swagger_ui',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
        $myprofileUrl = $this->router->generate(
            'app_index_index',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
        $userApiUrl = $this->router->generate(
            'app_api_users_getall',
            ['version' => 'v3'],
            UrlGenerator::ABSOLUTE_URL
        );
        $swaggerData['spec']['info']['description'] = $this->twig->render('swagger/description.markdown.twig', [
            'apiDocsUrl' => $apiDocsUrl,
            'myprofileUrl' => $myprofileUrl . 'myprofile',
            'userApiUrl' => $userApiUrl,
        ]);
        $swaggerData['spec']['info']['version'] = $this->apiVersion;
        return $swaggerData;
    }
}
