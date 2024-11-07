<?php

declare(strict_types=1);

namespace App\Service;

use Sentry\Tracing\SamplingContext;

class SentryTraceSampler
{
    private const array SKIP_ROUTES = ['app_ics_geticsfeed', 'app_download_downloadmaterials'];

    /**
     * Don't sample ICS or LM transactions as they are very noisy
     * everything else we sample 10% of
     */
    public function getTracesSampler(): callable
    {
        return function (SamplingContext $context): float {
            $tags = $context->getTransactionContext()->getTags();
            if (array_key_exists('route', $tags) && in_array($tags['route'], self::SKIP_ROUTES)) {
                return 0;
            }
            return 0.1;
        };
    }
}
