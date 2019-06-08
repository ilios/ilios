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
     * @param boolean $onlySuggest should the search return only suggestions
     * @return array
     * @throws \Exception when search is not configured
     */
    public function curriculumSearch(string $query, $onlySuggest)
    {
        if (!$this->enabled) {
            throw new \Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $fields = [
            'courseId',
            'courseYear',
            'courseTitle',
            'courseTerms',
            'courseObjectives',
            'courseLearningMaterials',
            'courseMeshDescriptors',
            'sessionId',
            'sessionTitle',
            'sessionDescription',
            'sessionType',
            'sessionTerms',
            'sessionObjectives',
            'sessionLearningMaterials',
            'sessionMeshDescriptors',
        ];

        $suggestFields = [
            'courseTitle',
            'courseTerms',
            'courseMeshDescriptors',
            'sessionTitle',
            'sessionType',
            'sessionTerms',
            'sessionMeshDescriptors',
        ];
        $suggest = array_reduce($suggestFields, function ($carry, $field) use ($query) {
            $carry[$field] = [
                'prefix' => $query,
                'completion' => [
                    'field' => "${field}.cmp",
                    'skip_duplicates' => true,
                ]
            ];

            return $carry;
        }, []);

        $params = [
            'type' => '_doc',
            'index' => self::PUBLIC_CURRICULUM_INDEX,
            'body' => [
                'suggest' => $suggest,
                "_source" => [
                    'courseId',
                    'courseTitle',
                    'courseYear',
                    'sessionId',
                    'sessionTitle'
                ],
                'sort' => '_score',
                'size' => 1000
            ]
        ];

        if (!$onlySuggest) {
            $should = array_map(function ($field) use ($query) {
                return [ 'match' => [ $field => ['query' => $query, '_name' => $field] ] ];
            }, $fields);
            $params['body']['query'] = [
                'bool' => [
                    'should' => $should,
                ]
            ];
        }

        $results = $this->search($params);

        $autocompleteSuggestions = array_reduce(
            $results['suggest'],
            function (array $carry, array $item) {
                $options = array_map(function (array $arr) {
                    return $arr['text'];
                }, $item[0]['options']);

                return array_unique(array_merge($carry, $options));
            },
            []
        );

        $mappedResults = array_map(function (array $arr) {
            $courseMatches = array_filter($arr['matched_queries'], function (string $match) {
                return strpos($match, 'course') === 0;
            });
            $sessionMatches = array_filter($arr['matched_queries'], function (string $match) {
                return strpos($match, 'session') === 0;
            });
            $rhett = $arr['_source'];
            $rhett['score'] = $arr['_score'];
            $rhett['courseMatches'] = $courseMatches;
            $rhett['sessionMatches'] = $sessionMatches;

            return $rhett;
        }, $results['hits']['hits']);

        $courses = array_reduce($mappedResults, function (array $carry, array $item) {
            $id = $item['courseId'];
            if (!array_key_exists($id, $carry)) {
                $carry[$id] = [
                    'id' => $id,
                    'title' => $item['courseTitle'],
                    'year' => $item['courseYear'],
                    'bestScore' => 0,
                    'sessions' => [],
                    'matchedIn' => [],
                ];
            }
            $courseMatches = array_map(function (string $match) {
                return strtolower(substr($match, strlen('course')));
            }, $item['courseMatches']);
            $sessionMatches = array_map(function (string $match) {
                return strtolower(substr($match, strlen('session')));
            }, $item['sessionMatches']);
            $carry[$id]['matchedIn'] += array_diff($courseMatches, $carry[$id]['matchedIn']);
            if ($item['score'] > $carry[$id]['bestScore']) {
                $carry[$id]['bestScore'] = $item['score'];
            }
            $carry[$id]['sessions'][] = [
                'id' => $item['sessionId'],
                'title' => $item['sessionTitle'],
                'score' => $item['score'],
                'matchedIn' => $sessionMatches,
            ];

            return $carry;
        }, []);

        usort($courses, function ($a, $b) {
            return $b['bestScore'] <=> $a['bestScore'];
        });

        return [
            'autocomplete' => $autocompleteSuggestions,
            'courses' => $courses
        ];
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
            'size' => $size,
            'body' => [
                'explain' => true,
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'type' => 'most_fields',
                        'fields' => [
                            'firstName',
                            'firstName.raw^3',
                            'middleName',
                            'middleName.raw^3',
                            'lastName',
                            'lastName.raw^3',
                            'displayName',
                            'displayName.raw^3',
                            'userName^5',
                            'campusId^5',
                            'email^5',
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
