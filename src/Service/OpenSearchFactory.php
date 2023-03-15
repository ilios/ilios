<?php

declare(strict_types=1);

namespace App\Service;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;

class OpenSearchFactory
{
    public static function getClient(Config $config): ?Client
    {
        $hosts = $config->get('search_hosts');
        if ($hosts) {
            $hosts = explode(';', $hosts);
            return (new ClientBuilder())->setHosts($hosts)->build();
        }

        return null;
    }
}
