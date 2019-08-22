<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use Exception;

class Search extends ElasticSearchBase
{
    /**
     * @param array $params
     * @return array
     * @throws Exception when the search service isn't setup
     */
    protected function search(array $params) : array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        return $this->client->search($params);
    }

    /**
     * @param string $id
     * @param array $params
     * @return array
     * @throws Exception when the search service isn't setup
     */
    protected function explain(string $id, array $params) : array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params['id'] = $id;
        return $this->client->explain($params);
    }

    /**
     * @param string $query
     * @param boolean $onlySuggest should the search return only suggestions
     * @return array
     * @throws Exception when search is not configured
     */
    public function curriculumSearch(string $query, $onlySuggest)
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }

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
                    'sessionTitle',
                    'school',
                ],
                'sort' => '_score',
                'size' => 1000
            ]
        ];

        if (!$onlySuggest) {
            $params['body']['query'] = $this->buildCurriculumSearch($query);
        }

        $results = $this->search($params);

        return $this->parseCurriculumSearchResults($results);
    }

    /**
     * @param string $query
     * @param int $size
     * @return array
     */
    public function userIdsQuery(string $query, int $size = 1000)
    {
        $results = $this->userSearch($query, $size, false);

        return array_column($results['users'], 'id');
    }

    /**
     * @param string $query
     * @param int $size
     * @return array
     * @throws Exception when search is not configured
     */
    public function userSearch(string $query, int $size, bool $onlySuggest)
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }

        $suggestFields = [
            'fullName',
            'fullNameLastFirst',
            'email.cmp',
            'campusId.cmp',
        ];
        $suggest = array_reduce($suggestFields, function ($carry, $field) use ($query) {
            $carry[$field] = [
                'prefix' => $query,
                'completion' => [
                    'field' => "${field}",
                    'skip_duplicates' => true,
                ]
            ];

            return $carry;
        }, []);


        $params = [
            'type' => '_doc',
            'index' => self::PRIVATE_USER_INDEX,
            'size' => $size,
            'body' => [
                'suggest' => $suggest,
                "_source" => [
                    'id',
                    'firstName',
                    'middleName',
                    'lastName',
                    'displayName',
                    'campusId',
                    'email',
                    'enabled',
                ],
                'sort' => '_score'
            ]
        ];

        if (!$onlySuggest) {
            $params['body']['query'] = [
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

        $users = array_column($results['hits']['hits'], '_source');

        return [
            'autocomplete' => $autocompleteSuggestions,
            'users' => $users
        ];
    }

    /**
     * @param string $query
     * @return array
     * @throws Exception when search is not configured
     */
    public function meshDescriptorIdsQuery(string $query)
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params = [
            'type' => '_doc',
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

    /**
     * Construct the query to search the curriculum
     * @param string $query
     * @return array
     */
    protected function buildCurriculumSearch(string $query) : array
    {
        $mustFields = [
            'courseId',
            'courseYear',
            'courseTitle',
            'courseTitle.ngram',
            'courseTerms',
            'courseTerms.ngram',
            'courseObjectives',
            'courseObjectives.ngram',
            'courseLearningMaterials',
            'courseLearningMaterials.ngram',
            'courseMeshDescriptors',
            'sessionId',
            'sessionTitle',
            'sessionTitle.ngram',
            'sessionDescription',
            'sessionDescription.ngram',
            'sessionType',
            'sessionTerms',
            'sessionTerms.ngram',
            'sessionObjectives',
            'sessionObjectives.ngram',
            'sessionLearningMaterials',
            'sessionLearningMaterials.ngram',
            'sessionMeshDescriptors',
        ];

        $shouldFields = [
            'courseTitle',
            'courseTerms',
            'courseObjectives',
            'courseLearningMaterials',
            'sessionTitle',
            'sessionDescription',
            'sessionType',
            'sessionTerms',
            'sessionObjectives',
            'sessionLearningMaterials',
        ];

        $mustMatch = array_map(function ($field) use ($query) {
            return [ 'match' => [ $field => [
                'query' => $query,
                '_name' => $field,
            ] ] ];
        }, $mustFields);

        /**
         * At least one of the mustMatch queries has to be a match
         * but we wrap it in a should block so they don't all have to match
         */
        $must = ['bool' => [
            'should' => $mustMatch
        ]];

        /**
         * The should queries are designed to boost the total score of
         * results that match more closely than the MUST set above so when
         * users enter a complete word like move it will score higher than
         * than a partial match on movement
         */
        $should = array_reduce(
            $shouldFields,
            function (array $carry, string $field) use ($query) {
                $matches = array_map(function (string $type) use ($field, $query) {
                    $fullField = "${field}.${type}";
                    return [ 'match' => [ $fullField => ['query' => $query, '_name' => $fullField] ] ];
                }, ['english', 'raw']);

                return array_merge($carry, $matches);
            },
            []
        );

        return [
            'bool' => [
                'must' => $must,
                'should' => $should,
            ]
        ];
    }

    protected function parseCurriculumSearchResults(array $results) : array
    {
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
                    'school' => $item['school'],
                    'bestScore' => 0,
                    'sessions' => [],
                    'matchedIn' => [],
                ];
            }
            $courseMatches = array_map(function (string $match) {
                $split = explode('.', $match);
                return strtolower(substr($split[0], strlen('course')));
            }, $item['courseMatches']);
            $sessionMatches = array_map(function (string $match) {
                $split = explode('.', $match);
                return strtolower(substr($split[0], strlen('session')));
            }, $item['sessionMatches']);
            $carry[$id]['matchedIn'] = array_unique(
                array_merge($courseMatches, $carry[$id]['matchedIn'])
            );
            if ($item['score'] > $carry[$id]['bestScore']) {
                $carry[$id]['bestScore'] = $item['score'];
            }
            $carry[$id]['sessions'][] = [
                'id' => $item['sessionId'],
                'title' => $item['sessionTitle'],
                'score' => $item['score'],
                'matchedIn' => array_unique(array_values($sessionMatches)),
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
}
