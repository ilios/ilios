<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ElasticSearchBase;
use Exception;

class Search extends ElasticSearchBase
{

    /**
     * @param string $query
     * @param bool $onlySuggest should the search return only suggestions
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
            'courseMeshDescriptorIds',
            'courseMeshDescriptorNames',
            'courseLearningMaterialTitles',
            'sessionTitle',
            'sessionType',
            'sessionTerms',
            'sessionMeshDescriptorIds',
            'sessionMeshDescriptorNames',
            'sessionLearningMaterialTitles',
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
            'index' => self::CURRICULUM_INDEX,
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
            'index' => self::MESH_INDEX,
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
    protected function buildCurriculumSearch(string $query): array
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
            'courseLearningMaterialTitles',
            'courseLearningMaterialTitles.ngram',
            'courseLearningMaterialDescriptions',
            'courseLearningMaterialDescriptions.ngram',
            'courseLearningMaterialCitation',
            'courseLearningMaterialCitation.ngram',
            'courseMeshDescriptorIds',
            'courseMeshDescriptorNames',
            'courseMeshDescriptorNames.ngram',
            'courseMeshDescriptorAnnotations',
            'courseMeshDescriptorAnnotations.ngram',
            'courseLearningMaterialAttachments',
            'courseLearningMaterialAttachments.ngram',
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
            'sessionLearningMaterialTitles',
            'sessionLearningMaterialTitles.ngram',
            'sessionLearningMaterialDescriptions',
            'sessionLearningMaterialDescriptions.ngram',
            'sessionLearningMaterialCitation',
            'sessionLearningMaterialCitation.ngram',
            'sessionMeshDescriptorIds',
            'sessionMeshDescriptorNames',
            'sessionMeshDescriptorNames.ngram',
            'sessionMeshDescriptorAnnotations',
            'sessionMeshDescriptorAnnotations.ngram',
            'sessionLearningMaterialAttachments',
            'sessionLearningMaterialAttachments.ngram',
        ];

        $shouldFields = [
            'courseTitle',
            'courseTerms',
            'courseObjectives',
            'courseLearningMaterialTitles',
            'courseLearningMaterialDescriptions',
            'courseLearningMaterialAttachments',
            'sessionTitle',
            'sessionDescription',
            'sessionType',
            'sessionTerms',
            'sessionObjectives',
            'sessionLearningMaterialTitles',
            'sessionLearningMaterialDescriptions',
            'sessionLearningMaterialAttachments',
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

    protected function parseCurriculumSearchResults(array $results): array
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
                $field = strtolower(substr($split[0], strlen('course')));
                if (strpos($field, 'meshdescriptor') !== false) {
                    $field = 'meshdescriptors';
                }
                if (strpos($field, 'learningmaterial') !== false) {
                    $field = 'learningmaterials';
                }

                return $field;
            }, $item['courseMatches']);
            $sessionMatches = array_map(function (string $match) {
                $split = explode('.', $match);
                $field = strtolower(substr($split[0], strlen('session')));
                if (strpos($field, 'meshdescriptor') !== false) {
                    $field = 'meshdescriptors';
                }
                if (strpos($field, 'learningmaterial') !== false) {
                    $field = 'learningmaterials';
                }

                return $field;
            }, $item['sessionMatches']);
            $carry[$id]['matchedIn'] = array_values(array_unique(
                array_merge($courseMatches, $carry[$id]['matchedIn'])
            ));
            if ($item['score'] > $carry[$id]['bestScore']) {
                $carry[$id]['bestScore'] = $item['score'];
            }
            $carry[$id]['sessions'][] = [
                'id' => $item['sessionId'],
                'title' => $item['sessionTitle'],
                'score' => $item['score'],
                'matchedIn' => array_values(array_unique($sessionMatches)),
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
