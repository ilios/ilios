<?php

namespace App\Classes;

use Elasticsearch\Client;

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
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        if ($client) {
            $this->enabled = true;
            $this->client = $client;
        }
    }

    public function isEnabled()
    {
        return (bool) $this->enabled;
    }
}
