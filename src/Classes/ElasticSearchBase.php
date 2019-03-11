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

    const COURSE_INDEX = 'ilios-public-courses';
    const MESH_INDEX = 'ilios-public-mesh';
    const USER_INDEX = 'ilios-private-users';

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
