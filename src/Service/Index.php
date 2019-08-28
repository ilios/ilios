<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Classes\IndexableCourse;
use App\Entity\DTO\UserDTO;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Exception;

class Index extends ElasticSearchBase
{
    /**
     * @param UserDTO[] $users
     * @return bool
     */
    public function indexUsers(array $users) : bool
    {
        foreach ($users as $user) {
            if (!$user instanceof UserDTO) {
                throw new \InvalidArgumentException(
                    '$users must be an array of ' . UserDTO::class . ' ' . get_class($user) . ' found'
                );
            }
        }
        $input = array_map(function (UserDTO $user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'middleName' => $user->middleName,
                'displayName' => $user->displayName,
                'email' => $user->email,
                'campusId' => $user->campusId,
                'username' => $user->username,
                'enabled' => $user->enabled,
                'fullName' => $user->firstName . ' ' . $user->middleName . ' ' . $user->lastName,
                'fullNameLastFirst' => $user->lastName . ', ' . $user->firstName . ' ' . $user->middleName,
            ];
        }, $users);

        $result = $this->bulkIndex(Search::PRIVATE_USER_INDEX, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id) : bool
    {
        $result = $this->delete([
            'index' => Search::PRIVATE_USER_INDEX,
            'id' => $id,
        ]);

        return !$result['errors'];
    }

    /**
     * @param IndexableCourse[] $courses
     * @return bool
     */
    public function indexCourses(array $courses) : bool
    {
        foreach ($courses as $course) {
            if (!$course instanceof IndexableCourse) {
                throw new \InvalidArgumentException(
                    '$courses must be an array of ' . IndexableCourse::class . ' ' . get_class($course) . ' found'
                );
            }
        }

        $input = array_reduce($courses, function (array $carry, IndexableCourse $item) {
            $sessions = $item->createIndexObjects();
            return array_merge($carry, $sessions);
        }, []);


        $result = $this->bulkIndex(Search::PUBLIC_CURRICULUM_INDEX, $input);

        if ($result['errors']) {
            $errors = array_map(function (array $item) {
                if (array_key_exists('error', $item['index'])) {
                    return $item['index']['error']['reason'];
                }
            }, $result['items']);
            $clean = array_filter($errors);
            $str = join(';', array_unique($clean));
            $count = count($clean);
            throw new Exception("Failed to index all courses ${count} errors. Error text: ${str}");
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteCourse(int $id) : bool
    {
        $result = $this->deleteByQuery([
            'index' => Search::PUBLIC_CURRICULUM_INDEX,
            'body' => [
                'query' => [
                    'term' => ['courseId' => $id]
                ]
            ]
        ]);

        return !count($result['failures']);
    }

    /**
     * @param Descriptor[] $descriptors
     * @return bool
     */
    public function indexMeshDescriptors(array $descriptors) : bool
    {
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof Descriptor) {
                throw new \InvalidArgumentException(
                    '$descriptors must be an array of ' . Descriptor::class . ' ' . get_class($descriptor) . ' found'
                );
            }
        }

        $input = array_map(function (Descriptor $descriptor) {
            $conceptMap = array_reduce($descriptor->getConcepts(), function (array $carry, Concept $concept) {
                $carry['conceptNames'][] = $concept->getName();
                $carry['scopeNotes'][] = $concept->getScopeNote();
                $carry['casn1Names'][] = $concept->getCasn1Name();
                foreach ($concept->getTerms() as $term) {
                    $carry['termNames'][] = $term->getName();
                }

                return $carry;
            }, [
                'conceptNames' => [],
                'termNames' => [],
                'scopeNotes' => [],
                'casn1Names' => [],
            ]);

            return [
                'id' => $descriptor->getUi(),
                'name' => $descriptor->getName(),
                'annotation' => $descriptor->getAnnotation(),
                'previousIndexing' => join(' ', $descriptor->getPreviousIndexing()),
                'terms' => join(' ', $conceptMap['termNames']),
                'concepts' => join(' ', $conceptMap['conceptNames']),
                'scopeNotes' => join(' ', $conceptMap['scopeNotes']),
                'casn1Names' => join(' ', $conceptMap['casn1Names']),
            ];
        }, $descriptors);

        $result = $this->bulkIndex(Search::PUBLIC_MESH_INDEX, $input);
        return !$result['errors'];
    }

    protected function index(array $params) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->index($params);
    }

    protected function delete(array $params) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->delete($params);
    }

    protected function deleteByQuery(array $params) : array
    {
        if (!$this->enabled) {
            return ['failures' => []];
        }
        return $this->client->deleteByQuery($params);
    }

    protected function bulk(array $params) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->bulk($params);
    }

    /**
     * The API for bulk indexing is a little bit weird and front data has to be inserted in
     * front of every item. This allows bulk indexing on many types at the same time, and
     * this convenience method takes care of that for us.
     * @param $index
     * @param array $items
     * @return array
     */
    protected function bulkIndex(string $index, array $items) : array
    {
        if (!$this->enabled || empty($items)) {
            return ['errors' => false];
        }
        // split the index into 2.5mb pieces so we don't run into the
        // AWS imposed 10MB per request limit
        $size = strlen(serialize($items));
        $parts = ceil($size / 4000000);
        $chunkCounts = ceil(count($items) / $parts);
        $chunks = array_chunk($items, $chunkCounts);

        $results = [
            'took' => 0,
            'errors' => false,
            'items' => []
        ];

        foreach ($chunks as $chunk) {
            $body = [];
            foreach ($chunk as $item) {
                $body[] = ['index' => [
                    '_index' => $index,
                    '_type' => '_doc',
                    '_id' => $item['id']
                ]];
                $body[] = $item;
            }
            $rhett = $this->bulk(['body' => $body]);
            $results['took'] += $rhett['took'];
            if ($rhett['errors']) {
                $results['errors'] = true;
            }
            $results['items'] = array_merge($results['items'], $rhett['items']);
        }

        return $results;
    }

    public function clear()
    {
        if (!$this->enabled) {
            return;
        }

        $indexes = [
            $this->buildCurriculumIndex(),
            $this->buildUserIndex(),
            [
                'index' => self::PUBLIC_MESH_INDEX,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                    ],
                ]
            ],
        ];
        foreach ($indexes as $params) {
            if ($this->client->indices()->exists(['index' => $params['index']])) {
                $this->client->indices()->delete(['index' => $params['index']]);
            }
            $this->client->indices()->create($params);
        }
    }

    /**
     * Create a set of common analyzers we can use in multiple indexes
     * @return array
     */
    protected function buildAnalyzers() : array
    {
        return [
            'analyzer' => [
                'edge_ngram_analyzer' => [
                    'tokenizer' => 'edge_ngram_tokenizer',
                    'filter' => ['lowercase'],
                ],
                'ngram_analyzer' => [
                    'tokenizer' => 'ngram_tokenizer',
                    'filter' => ['lowercase'],
                ],
                'string_search_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'keyword',
                    'filter' => ['lowercase', 'word_delimiter'],
                ],
            ],
            'tokenizer' => [
                'edge_ngram_tokenizer' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 3,
                    'max_gram' => 15,
                    'token_chars' => [
                        'letter',
                        'digit'
                    ],
                ],
                'ngram_tokenizer' => [
                    'type' => 'ngram',
                    'min_gram' => 3,
                    'max_gram' => 15,
                    'token_chars' => [
                        'letter',
                        'digit'
                    ],
                ],
            ],
        ];
    }

    /**
     * Create the index for curriculum
     * @return array
     */
    protected function buildCurriculumIndex() : array
    {
        $txtTypeField = [
            'type' => 'text',
            'analyzer' => 'standard',
            'fields' => [
                'ngram' => [
                    'type' => 'text',
                    'analyzer' => 'ngram_analyzer',
                    'search_analyzer' => 'string_search_analyzer',
                ],
                'english' => [
                    'type' => 'text',
                    'analyzer' => 'english',
                ],
                'raw' => [
                    'type' => 'text',
                    'analyzer' => 'keyword',
                ]
            ],
        ];
        $txtTypeFieldWithCompletion = $txtTypeField;
        $txtTypeFieldWithCompletion['fields']['cmp'] = ['type' => 'completion'];

        $analysis = $this->buildAnalyzers();
        return [
            'index' => self::PUBLIC_CURRICULUM_INDEX,
            'body' => [
                'settings' => [
                    'analysis' => $analysis,
                    'max_ngram_diff' =>  15,
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    '_doc' => [
                        'properties' => [
                            'courseId' => [
                                'type' => 'keyword',
                            ],
                            'school' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion'
                                    ]
                                ],
                            ],
                            'courseYear' => [
                                'type' => 'keyword',
                            ],
                            'courseTitle' => $txtTypeFieldWithCompletion,
                            'courseTerms' => $txtTypeFieldWithCompletion,
                            'courseObjectives'  => $txtTypeField,
                            'courseLearningMaterials'  => $txtTypeField,
                            'courseMeshDescriptorIds' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion',
                                        // we have to override the analyzer here because the default strips
                                        // out numbers and mesh ids are mostly numbers
                                        'analyzer' => 'standard',
                                    ]
                                ],
                            ],
                            'courseMeshDescriptorNames' => $txtTypeFieldWithCompletion,
                            'courseMeshDescriptorAnnotations' => $txtTypeField,
                            'sessionId' => [
                                'type' => 'keyword',
                            ],
                            'sessionTitle' => $txtTypeFieldWithCompletion,
                            'sessionDescription' => $txtTypeField,
                            'sessionType' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion'
                                    ]
                                ],
                            ],
                            'sessionTerms' => $txtTypeFieldWithCompletion,
                            'sessionObjectives'  => $txtTypeField,
                            'sessionLearningMaterials'  => $txtTypeField,
                            'sessionMeshDescriptorIds' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion',
                                        // we have to override the analyzer here because the default strips
                                        // out numbers and mesh ids are mostly numbers
                                        'analyzer' => 'standard',
                                    ]
                                ],
                            ],
                            'sessionMeshDescriptorNames' => $txtTypeFieldWithCompletion,
                            'sessionMeshDescriptorAnnotations' => $txtTypeField,
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function buildUserIndex() : array
    {
        $analysis = $this->buildAnalyzers();
        return [
            'index' => self::PRIVATE_USER_INDEX,
            'body' => [
                'settings' => [
                    'analysis' => $analysis,
                    'max_ngram_diff' =>  15,
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    '_doc' => [
                        'properties' => [
                            'id' => [
                                'type' => 'keyword',
                            ],
                            'firstName' => [
                                'type' => 'text',
                                'analyzer' => 'edge_ngram_analyzer',
                                'search_analyzer' => 'string_search_analyzer',
                                'fields' => [
                                    'raw' => [
                                        'type' => 'keyword',
                                    ]
                                ],
                            ],
                            'middleName' => [
                                'type' => 'text',
                                'analyzer' => 'edge_ngram_analyzer',
                                'search_analyzer' => 'string_search_analyzer',
                                'fields' => [
                                    'raw' => [
                                        'type' => 'keyword',
                                    ]
                                ],
                            ],
                            'lastName' => [
                                'type' => 'text',
                                'analyzer' => 'edge_ngram_analyzer',
                                'search_analyzer' => 'string_search_analyzer',
                                'fields' => [
                                    'raw' => [
                                        'type' => 'keyword',
                                    ]
                                ],
                            ],
                            'displayName' => [
                                'type' => 'text',
                                'analyzer' => 'edge_ngram_analyzer',
                                'search_analyzer' => 'string_search_analyzer',
                                'fields' => [
                                    'raw' => [
                                        'type' => 'keyword',
                                    ],
                                    'cmp' => [
                                        'type' => 'completion'
                                    ],
                                ],
                            ],
                            'fullName' => [
                                'type' => 'completion'
                            ],
                            'fullNameLastFirst' => [
                                'type' => 'completion'
                            ],
                            'username' => [
                                'type' => 'keyword',
                            ],
                            'campusId' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion'
                                    ]
                                ],
                            ],
                            'email' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion'
                                    ]
                                ],
                            ],
                            'enabled' => [
                                'type' => 'boolean',
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}
