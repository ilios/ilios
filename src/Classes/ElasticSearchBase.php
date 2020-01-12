<?php

declare(strict_types=1);

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

    public const CURRICULUM_INDEX = 'ilios-curriculum';
    public const USER_INDEX = 'ilios-users';
    public const MESH_INDEX = 'ilios-mesh';
    public const LEARNING_MATERIAL_INDEX = 'ilios-learning-materials';
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
