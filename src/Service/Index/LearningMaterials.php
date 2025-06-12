<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;
use App\Entity\DTO\LearningMaterialDTO;
use App\Message\CourseIndexRequest;
use App\Repository\LearningMaterialRepository;
use App\Service\Config;
use App\Service\NonCachingIliosFileSystem;
use Exception;
use OpenSearch\Client;
use InvalidArgumentException;
use stdClass;
use Symfony\Component\Messenger\MessageBusInterface;

class LearningMaterials extends OpenSearchBase
{
    public const string INDEX = 'ilios-learning-materials';

    public function __construct(
        private NonCachingIliosFileSystem $nonCachingIliosFileSystem,
        private MessageBusInterface $bus,
        private LearningMaterialRepository $learningMaterialRepository,
        Config $config,
        ?Client $client = null
    ) {
        parent::__construct($config, $client);
    }

    public function index(array $materials, bool $force = false): bool
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        foreach ($materials as $material) {
            if (!$material instanceof LearningMaterialDTO) {
                throw new InvalidArgumentException(
                    sprintf(
                        '$materials must be an array of %s. %s found',
                        LearningMaterialDTO::class,
                        $material::class
                    )
                );
            }
            if (!$material->relativePath) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Material %d has no relative path and cannot be indexed, probably not a file type material.',
                        $material->id,
                    )
                );
            }
        }

        $ids = array_map(fn (LearningMaterialDTO $dto) => $dto->id, $materials);

        if ($force) {
            $skipIds = [];
        } else {
            $skipIds = $this->alreadyIndexedMaterials($ids);
        }

        $materialToIndex = array_filter(
            $materials,
            fn(LearningMaterialDTO $dto) => !in_array($dto->id, $skipIds)
        );

        $input = array_map(
            function (LearningMaterialDTO $lm) {
                $path =  $this->nonCachingIliosFileSystem->getLearningMaterialTextPath($lm->relativePath);
                $contents = $this->nonCachingIliosFileSystem->getFileContents($path);

                $clean = $contents ? $this->cleanMaterialText($lm->id, $contents) : '';
                return [
                    'id' => 'lm_' . $lm->id,
                    'learningMaterialId' => $lm->id,
                    'title' => $lm->title,
                    'description' => $lm->description,
                    'filename' => $lm->filename,
                    'contents' => $clean,
                ];
            },
            $materialToIndex,
        );

        if (!$this->doBulkIndex(self::INDEX, array_values($input))) {
            return false;
        }

        //re-index the courses for every newly indexed material
        $ids = array_column($materialToIndex, 'id');
        $associatedCourses = $this->learningMaterialRepository->getCourseIdsForMaterials($ids);
        $chunks = array_chunk($associatedCourses, CourseIndexRequest::MAX_COURSES);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new CourseIndexRequest($ids));
        }

        return true;
    }

    protected function cleanMaterialText(int $id, string $str): string
    {
        //remove punctuation
        $str = preg_replace('/[\p{P}]/u', '', $str);
        if ($str === null) {
            throw new Exception(
                "Unable to clean material #{$id}, error: " .
                preg_last_error_msg()
            );
        }

        //remove symbols, keeping letters, numbers, and spaces
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);
        if ($str === null) {
            throw new Exception(
                "Unable to clean material #{$id}, error: " .
                preg_last_error_msg()
            );
        }

        //remove extra spaces
        $str = preg_replace('/\s+/u', ' ', $str);
        if ($str === null) {
            throw new Exception(
                "Unable to clean material #{$id}, error: " .
                preg_last_error_msg()
            );
        }

        return trim($str);
    }

    protected function alreadyIndexedMaterials(array $ids): array
    {
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'terms' => [
                                    'learningMaterialId' => array_values($ids),
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'learningMaterialId' => [
                        'terms' => [
                            'field' => 'learningMaterialId',
                            'size' => self::SIZE_LIMIT,
                        ],
                    ],
                ],
                'size' => self::SIZE_LIMIT,
            ],
        ];
        $results = $this->doSearch($params);

        return  array_column($results['aggregations']['learningMaterialId']['buckets'], 'key');
    }

    public function getAllIds(): array
    {
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                '_source' => ['learningMaterialId'],
            ],
            'size' => self::SIZE_LIMIT,
        ];

        $results = $this->doScrollSearch($params);
        return array_map(fn ($item) => $item['_source']['learningMaterialId'], $results);
    }

    public function delete(int $id): bool
    {
        $result = $this->doDeleteByQuery([
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'term' => ['learningMaterialId' => $id],
                ],
            ],
        ]);

        return !count($result['failures']);
    }

    public static function getMapping(): array
    {
        $txtTypeField = [
            'type' => 'text',
            'analyzer' => 'standard',
            'fields' => [
                'english' => [
                    'type' => 'text',
                    'analyzer' => 'english',
                ],
                'french' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'spanish' => [
                    'type' => 'text',
                    'analyzer' => 'spanish',
                ],
            ],
        ];
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
            ],
            'mappings' => [
                '_meta' => [
                    'version' => '2',
                ],
                'properties' => [
                    'learningMaterialId' => [
                        'type' => 'integer',
                    ],
                    'title' => $txtTypeField,
                    'description' => $txtTypeField,
                    'filename' => [
                        'type' => 'text',
                        'analyzer' => 'keyword',
                    ],
                    'contents' => $txtTypeField,
                ],
            ],
        ];
    }
}
