<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use Ilios\MeSH\Model\Descriptor;

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
    public function curriculumSearch(string $query)
    {
        if (!$this->enabled) {
            throw new \Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        return [];
    }

    /**
     * @param string $query
     * @param int $size
     * @return array
     * @throws \Exception when search is not configured
     */
    public function userIdsQuery(string $query, int $size = 1000)
    {
        if (!$this->enabled) {
            throw new \Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params = [
            'type' => '_doc',
            'index' => self::PRIVATE_USER_INDEX,
            "size" => $size,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'multi_match' => [
                                'query' => $query,
                                'type' => "cross_fields",
                                'fields' => [
                                    'user.fullName^2',
                                    'user.email',
                                    'user.campusId',
                                    'user.username'
                                ]
                            ]
                        ],
                        'should' => [
                            'match_phrase' => [
                                'fullName' => [
                                    'query' => $query,
                                    'slop'  => 100
                                ],
                            ]
                        ]
                    ]
                ],
                "_source" => [
                    '_id'
                ],
                'sort' => '_score'
            ]
        ];
        $results = $this->search($params);
        return array_map(function (array $arr) {
            return $arr['_id'];
        }, $results['hits']['hits']);
    }

    /**
     * @param string $query
     * @return array
     * @throws \Exception when search is not configured
     */
    public function meshDescriptorIdsQuery(string $query)
    {
        if (!$this->enabled) {
            throw new \Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params = [
            'type' => Descriptor::class,
            'index' => self::PUBLIC_MESH_INDEX,
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
