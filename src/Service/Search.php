<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\UserDTO;

class Search extends ElasticSearchBase
{
    /**
     * @param array $params
     * @return array
     * @throws \Exception when the search service isn't setup
     */
    protected function search(array $params) : array
    {
        if (!$this->enabled) {
            throw new \Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        return $this->client->search($params);
    }

    /**
     * @param string $query
     * @return array
     * @throws \Exception when search is not configured
     */
    public function userIdsQuery(string $query)
    {
        if (!$this->enabled) {
            throw new \Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params = [
            'type' => UserDTO::class,
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
