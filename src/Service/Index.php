<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Classes\IndexableCourse;
use App\Entity\DTO\UserDTO;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;

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

        return !$result['errors'];
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
        $body = [];
        foreach ($items as $item) {
            $body[] = ['index' => [
                '_index' => $index,
                '_type' => '_doc',
                '_id' => $item['id']
            ]];
            $body[] = $item;
        }
        return $this->bulk(['body' => $body]);
    }

    public function clear()
    {
        if (!$this->enabled) {
            return;
        }
        $analysis = [
            'analyzer' => [
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
                'ngram_tokenizer' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 3,
                    'max_gram' => 10,
                ],
            ],
        ];

        $indexes = [
            [
                'index' => self::PUBLIC_CURRICULUM_INDEX,
                'body' => [
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
                                'courseTitle' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ],
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                                'courseTerms' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ],
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                                'courseObjectives' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ]
                                    ],
                                ],
                                'courseLearningMaterials' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ]
                                    ],
                                ],
                                'courseMeshDescriptors' => [
                                    'type' => 'keyword',
                                    'fields' => [
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                                'sessionId' => [
                                    'type' => 'keyword',
                                ],
                                'sessionTitle' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ],
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                                'sessionDescription' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ]
                                    ],
                                ],
                                'sessionType' => [
                                    'type' => 'keyword',
                                    'fields' => [
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                                'sessionTerms' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ],
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                                'sessionObjectives' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ]
                                    ],
                                ],
                                'sessionLearningMaterials' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
                                        ]
                                    ],
                                ],
                                'sessionMeshDescriptors' => [
                                    'type' => 'keyword',
                                    'fields' => [
                                        'cmp' => [
                                            'type' => 'completion'
                                        ]
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            [
                'index' => self::PRIVATE_USER_INDEX,
                'body' => [
                    'settings' => [
                        'analysis' => $analysis,
                    ],
                    'mappings' => [
                        '_doc' => [
                            'properties' => [
                                'id' => [
                                    'type' => 'keyword',
                                ],
                                'firstName' => [
                                    'type' => 'text',
                                    'analyzer' => 'ngram_analyzer',
                                    'search_analyzer' => 'string_search_analyzer',
                                    'fields' => [
                                        'raw' => [
                                            'type' => 'keyword',
                                        ]
                                    ],
                                ],
                                'middleName' => [
                                    'type' => 'text',
                                    'analyzer' => 'ngram_analyzer',
                                    'search_analyzer' => 'string_search_analyzer',
                                    'fields' => [
                                        'raw' => [
                                            'type' => 'keyword',
                                        ]
                                    ],
                                ],
                                'lastName' => [
                                    'type' => 'text',
                                    'analyzer' => 'ngram_analyzer',
                                    'search_analyzer' => 'string_search_analyzer',
                                    'fields' => [
                                        'raw' => [
                                            'type' => 'keyword',
                                        ]
                                    ],
                                ],
                                'displayName' => [
                                    'type' => 'text',
                                    'analyzer' => 'ngram_analyzer',
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
            ],
            [
                'index' => self::PUBLIC_MESH_INDEX,
            ],
        ];
        foreach ($indexes as $params) {
            if ($this->client->indices()->exists(['index' => $params['index']])) {
                $this->client->indices()->delete(['index' => $params['index']]);
            }
            $this->client->indices()->create($params);
        }
    }
}
