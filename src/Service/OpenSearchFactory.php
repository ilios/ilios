<?php

declare(strict_types=1);

namespace App\Service;

use OpenSearch\Client;
use OpenSearch\SymfonyClientFactory;

class OpenSearchFactory
{
    public static function getClient(Config $config): ?Client
    {
        $hosts = $config->get('search_hosts');
        $host = $config->get('search_host');
        if ($host || $hosts) {
            if (!$host) {
                //paper over different config as search_hosts is deprecated, but not removed
                $hosts = explode(';', $hosts);
                $host = $hosts[0];
            }
            return (new SymfonyClientFactory())->create([
                'base_uri' => $host,
            ]);
        }

        return null;
    }
}
