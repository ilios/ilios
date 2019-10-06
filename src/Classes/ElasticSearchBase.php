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
     * @var bool
     */
    protected $enabled = false;

    public const PUBLIC_CURRICULUM_INDEX = 'ilios-public-curriculum';
    public const PRIVATE_USER_INDEX = 'ilios-private-users';
    public const PUBLIC_MESH_INDEX = 'ilios-public-mesh';
    public const PRIVATE_LEARNING_MATERIAL_INDEX = 'ilios-private-learning-materials';
    public const SESSION_ID_PREFIX = 'session_';

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
