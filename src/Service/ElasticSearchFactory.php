<?php

namespace App\Service;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticSearchFactory
{
    public static function getClient(Config $config) : ?Client
    {
        $elasticSearchHosts = $config->get('elasticsearch_hosts');
        if ($elasticSearchHosts) {
            $hosts = explode(';', $elasticSearchHosts);
            return ClientBuilder::create()->setHosts($hosts)->build();
        }

        return null;
    }
}
