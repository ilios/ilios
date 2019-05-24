<?php

namespace App\Classes;

use App\Service\Config;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticSearchBase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var boolean
     */
    protected $enabled = false;

    const PUBLIC_CURRICULUM_INDEX = 'ilios-public-curriculum';
    const PRIVATE_USER_INDEX = 'ilios-private-users';
    const PUBLIC_MESH_INDEX = 'ilios-public-mesh';
    const SESSION_ID_PREFIX = 'session_';

    /**
     * Search constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $elasticSearchHosts = $config->get('elasticsearch_hosts');
        if ($elasticSearchHosts) {
            $this->enabled = true;
            $hosts = explode(';', $elasticSearchHosts);
            $this->client = ClientBuilder::create()->setHosts($hosts)->build();
        }
    }

    public function isEnabled()
    {
        return (bool) $this->enabled;
    }
}
