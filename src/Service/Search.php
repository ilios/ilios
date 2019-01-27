<?php

namespace App\Service;

use App\Entity\User;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class Search
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var boolean
     */
    protected $enabled = false;

    const PUBLIC_INDEX = 'ilios-public';
    const PRIVATE_INDEX = 'ilios-private';

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

    public function index(array $params) : array
    {
        return $this->client->index($params);
    }

    public function bulk(array $params) : array
    {
        return $this->client->bulk($params);
    }

    /**
     * The API for bulk indexing is a little bit weird and front data has to be inserted in
     * front of every item. This allows bulk indexing on many types at the same time, but
     * this convenience method takes care of that for us.
     * @param $index
     * @param $type
     * @param array $items
     * @return array
     */
    public function bulkIndex(string $index, string $type, array $items) : array
    {
        $body = [];
        foreach ($items as $item) {
            $body[] = ['index' => [
                '_index' => $index,
                '_type' => $type,
                '_id' => $item['id']
            ]];
            $body[] = $item;
        }
        return $this->bulk(['body' => $body]);
    }

    public function search(array $params) : array
    {
        return $this->client->search($params);
    }

    public function clear()
    {
        if ($this->client->indices()->exists(['index' => self::PUBLIC_INDEX])) {
            $this->client->indices()->delete(['index' => self::PUBLIC_INDEX]);
        }
        if ($this->client->indices()->exists(['index' => self::PRIVATE_INDEX])) {
            $this->client->indices()->delete(['index' => self::PRIVATE_INDEX]);
        }
        $this->client->indices()->create(['index' => self::PUBLIC_INDEX]);
        $this->client->indices()->create(['index' => self::PRIVATE_INDEX]);
    }

    public function userIdsQuery(string $query)
    {
        $params = [
            'type' => User::class,
            'index' => self::PRIVATE_INDEX,
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => "*${query}*",
                    ]
                ],
                "_source" => [
                    '_id'
                ]
            ]
        ];
        $results = $this->search($params);
        return array_map(function (array $arr) {
            return $arr['_id'];
        }, $results['hits']['hits']);
    }
}
