<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait GetUrlTrait
 * Extracted from https://git.io/fjzsa
 */
trait GetUrlTrait
{
    public function getUrl(
        KernelBrowser $browser,
        string $route,
        array $params = [],
        int $absolute = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $browser->getContainer()->get('router')->generate($route, $params, $absolute);
    }
}
