<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Classes\IndexableCourse;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\UserDTO;
use Elasticsearch\Client;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Exception;

class Index extends ElasticSearchBase
{
    /**
     * @var IliosFileSystem
     */
    private $iliosFileSystem;

    public function __construct(IliosFileSystem $iliosFileSystem, Client $client = null)
    {
        parent::__construct($client);
        $this->iliosFileSystem = $iliosFileSystem;
    }

    /**
     * @param UserDTO[] $users
     * @return bool
     */
    public function indexUsers(array $users): bool
    {
        foreach ($users as $user) {
            if (!$user instanceof UserDTO) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$users must be an array of %s. %s found',
                        UserDTO::class,
                        get_class($user)
                    )
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
    public function deleteUser(int $id): bool
    {
        $result = $this->delete([
            'index' => Search::PRIVATE_USER_INDEX,
            'id' => $id,
        ]);

        return $result['result'] === 'deleted';
    }

    /**
     * @param IndexableCourse[] $courses
     * @return bool
     */
    public function indexCourses(array $courses): bool
    {
        foreach ($courses as $course) {
            if (!$course instanceof IndexableCourse) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$courses must be an array of %s. %s found',
                        IndexableCourse::class,
                        get_class($course)
                    )
                );
            }
        }

        $input = array_reduce($courses, function (array $carry, IndexableCourse $item) {
            $sessions = $item->createIndexObjects();
            $sessionsWithMaterials = $this->attachLearningMaterialsToSession($sessions);
            return array_merge($carry, $sessionsWithMaterials);
        }, []);

        $result = $this->bulkIndex(Search::CURRICULUM_INDEX, $input);

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
    public function deleteCourse(int $id): bool
    {
        $result = $this->deleteByQuery([
            'index' => Search::CURRICULUM_INDEX,
            'body' => [
                'query' => [
                    'term' => ['courseId' => $id]
                ]
            ]
        ]);

        return !count($result['failures']);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteSession(int $id): bool
    {
        $result = $this->delete([
            'index' => Search::CURRICULUM_INDEX,
            'id' => ElasticSearchBase::SESSION_ID_PREFIX . $id
        ]);

        return $result['result'] === 'deleted';
    }

    /**
     * @param Descriptor[] $descriptors
     * @return bool
     */
    public function indexMeshDescriptors(array $descriptors): bool
    {
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof Descriptor) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$descriptors must be an array of %s. %s found',
                        Descriptor::class,
                        get_class($descriptor)
                    )
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

    /**
     * @param LearningMaterialDTO[] $materials
     * @return bool
     */
    public function indexLearningMaterials(array $materials): bool
    {
        foreach ($materials as $material) {
            if (!$material instanceof LearningMaterialDTO) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$materials must be an array of %s. %s found',
                        LearningMaterialDTO::class,
                        get_class($material)
                    )
                );
            }
        }
        $results = array_map(function (LearningMaterialDTO $lm) {
            $params = [
                'index' => self::PRIVATE_LEARNING_MATERIAL_INDEX,
                'type' => '_doc',
                'pipeline' => 'learning_materials',
                'id' => $lm->id,
                'body' => [
                    'data' => base64_encode($this->iliosFileSystem->getFileContents($lm->relativePath))
                ]
            ];
            return $this->client->index($params);
        }, $materials);

        $errors = array_filter($results, function ($result) {
            return $result['result'] === 'error';
        });

        return empty($errors);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteLearningMaterial(int $id): bool
    {
        $result = $this->delete([
            'index' => Search::PRIVATE_LEARNING_MATERIAL_INDEX,
            'id' => $id
        ]);

        return $result['result'] === 'deleted';
    }

    protected function index(array $params): array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->index($params);
    }

    protected function delete(array $params): array
    {
        if (!$this->enabled) {
            return ['result' => 'deleted'];
        }
        return $this->client->delete($params);
    }

    protected function deleteByQuery(array $params): array
    {
        if (!$this->enabled) {
            return ['failures' => []];
        }
        return $this->client->deleteByQuery($params);
    }

    protected function bulk(array $params): array
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
    protected function bulkIndex(string $index, array $items): array
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
        // remove the deprecated public-curriculum-index
        if ($this->client->indices()->exists(['index' => self::PUBLIC_CURRICULUM_INDEX])) {
            $this->client->indices()->delete(['index' => self::PUBLIC_CURRICULUM_INDEX]);
        }

        $learningMaterialsPipeline = $this->buildLearningMaterialPipeline();
        $this->client->ingest()->putPipeline($learningMaterialsPipeline);
        $indexes = [
            $this->buildCurriculumIndex(),
            $this->buildUserIndex(),
            $this->buildLearningMaterialIndex(),
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
    protected function buildAnalyzers(): array
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
                'email_address' => [
                    'type' => 'custom',
                    'tokenizer' => 'uax_url_email',
                    'filter' => ['lowercase', 'stop'],
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
    protected function buildCurriculumIndex(): array
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
            'index' => self::CURRICULUM_INDEX,
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
                            'courseLearningMaterialTitles'  => $txtTypeFieldWithCompletion,
                            'courseLearningMaterialDescriptions'  => $txtTypeField,
                            'courseLearningMaterialCitation'  => $txtTypeField,
                            'courseLearningMaterialAttachments'  => $txtTypeField,
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
                            'sessionLearningMaterialTitles'  => $txtTypeFieldWithCompletion,
                            'sessionLearningMaterialDescriptions'  => $txtTypeField,
                            'sessionLearningMaterialCitation'  => $txtTypeField,
                            'sessionLearningMaterialAttachments'  => $txtTypeField,
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

    protected function buildUserIndex(): array
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
                            'campusId' => [
                                'type' => 'keyword',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion'
                                    ]
                                ],
                            ],
                            'email' => [
                                'type' => 'text',
                                'analyzer' => 'standard',
                                'search_analyzer' => 'email_address',
                                'fields' => [
                                    'cmp' => [
                                        'type' => 'completion',
                                    ],
                                    'email' => [
                                        'type' => 'text',
                                        'analyzer' => 'email_address',
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
    
    protected function buildLearningMaterialIndex(): array
    {
        return [
            'index' => self::PRIVATE_LEARNING_MATERIAL_INDEX,
            'body' => [
                'settings' => [
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    '_doc' => [
                        'properties' => [
                            'material' => [
                                'type' => 'object'
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function buildLearningMaterialPipeline(): array
    {
        return [
            'id' => 'learning_materials',
            'body' => [
                'description' => 'Learning Material Data',
                'processors' => [
                    [
                        'attachment' => [
                            'field' => 'data',
                            'target_field' => 'material',
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function attachLearningMaterialsToSession(array $sessions): array
    {
        $courseIds = array_column($sessions, 'courseFileLearningMaterialIds');
        $sessionIds = array_column($sessions, 'sessionFileLearningMaterialIds');
        $learningMaterialIds = array_values(array_unique(array_merge([], ...$courseIds, ...$sessionIds)));
        $params = [
            'type' => '_doc',
            'index' => self::PRIVATE_LEARNING_MATERIAL_INDEX,
            'body' => [
                'query' => [
                    'ids' => [
                        'values' => $learningMaterialIds
                    ]
                ],
                "_source" => [
                    '_id',
                    'material.content',
                ]
            ]
        ];
        $results = $this->client->search($params);

        $materialsById = array_reduce($results['hits']['hits'], function (array $carry, array $hit) {
            $result = $hit['_source'];
            $id = $hit['_id'];

            if (array_key_exists('material', $result)) {
                $carry[$id] = $result['material']['content'];
            }

            return $carry;
        }, []);
        return array_map(function (array $session) use ($materialsById) {
            foreach ($session['sessionFileLearningMaterialIds'] as $id) {
                if (array_key_exists($id, $materialsById)) {
                    $session['sessionLearningMaterialAttachments'][] = $materialsById[$id];
                }
            }
            unset($session['sessionFileLearningMaterialIds']);
            foreach ($session['courseFileLearningMaterialIds'] as $id) {
                if (array_key_exists($id, $materialsById)) {
                    $session['courseLearningMaterialAttachments'][] = $materialsById[$id];
                }
            }
            unset($session['courseFileLearningMaterialIds']);

            return $session;
        }, $sessions);
    }
}
