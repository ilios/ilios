<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait GetUrlTrait
 * Extracted from https://git.io/fjzsa
 */
trait GetUrlTrait
{
    /**
     * @param KernelBrowser $browser
     * @param string $route
     * @param array $params
     * @param int $absolute
     *
     * @return string
     */
    public function getUrl(
        KernelBrowser $browser,
        $route,
        $params = [],
        $absolute = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        return $browser->getContainer()->get('router')->generate($route, $params, $absolute);
    }
}
