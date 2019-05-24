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
        $input = [];

        foreach ($courses as $course) {
            $courseData = [
                'courseId' => $course->courseDTO->id,
                'school' => $course->school,
                'courseYear' => $course->courseDTO->year,
                'courseTitle' => $course->courseDTO->title,
                'courseExternalId' => $course->courseDTO->externalId,
                'clerkshipType' => $course->clerkshipType,
                'courseDirectors' => implode(' ', $course->courseDirectors),
                'courseAdministrators' => implode(' ', $course->courseAdministrators),
                'courseObjectives' => implode(' ', $course->courseObjectives),
                'courseTerms' => implode(' ', $course->courseTerms),
                'courseMeshDescriptors' => implode(' ', $course->courseMeshDescriptors),
                'courseLearningMaterials' => implode(' ', $course->courseLearningMaterials),
            ];

            foreach ($course->sessions as $session) {
                $sessionData = [
                    'id' => self::SESSION_ID_PREFIX . $session['sessionId'],
                    'sessionId' => $session['sessionId'],
                    'sessionTitle' => $session['title'],
                    'sessionType' => $session['sessionType'],
                    'sessionDescription' => $session['description'],
                    'sessionAdministrators' => implode(' ', $session['administrators']),
                    'sessionObjectives' => implode(' ', $session['objectives']),
                    'sessionTerms' => implode(' ', $session['terms']),
                    'sessionMeshDescriptors' => implode(' ', $session['meshDescriptors']),
                    'sessionLearningMaterials' => implode(' ', $session['learningMaterials']),
                ];

                $input[] = array_merge($courseData, $sessionData);
            }
        }

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
        if (!$this->enabled) {
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

        $indexes = [
            [
                'index' => self::PUBLIC_CURRICULUM_INDEX,
                'body' => [
                    'mappings' => [
                        '_doc' => [
                            'properties' => [
                                'school' => [
                                    'type' => 'keyword',
                                ],
                                'courseYear' => [
                                    'type' => 'integer',
                                ],
                                'courseTitle' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
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
                                ],
                                'sessionTitle' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
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
                                ],
                                'sessionTerms' => [
                                    'type' => 'text',
                                    'analyzer' => 'english',
                                    'fields' => [
                                        'std' => [
                                            'type' => 'text',
                                            'analyzer' => 'standard',
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
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            [
                'index' => self::PRIVATE_USER_INDEX,
                'body' => [
                    'mappings' => [
                        '_doc' => [
                            'properties' => [
                                'firstName' => [
                                    'type' => 'text',
                                    'copy_to' => 'fullName'
                                ],
                                'lastName' => [
                                    'type' => 'text',
                                    'copy_to' => 'fullName'
                                ],
                                'middleName' => [
                                    'type' => 'text',
                                    'copy_to' => 'fullName'
                                ],
                                'username' => [
                                    'type' => 'keyword',
                                ],
                                'fullName' => [
                                    'type' => 'text',
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
