<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;
use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\NonCachingIliosFileSystem;
use Exception;
use OpenSearch\Client;
use InvalidArgumentException;

class LearningMaterials extends OpenSearchBase
{
    public const string INDEX = 'ilios-learning-materials';

    public function __construct(
        private NonCachingIliosFileSystem $nonCachingIliosFileSystem,
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

        $input = array_map(function (LearningMaterialDTO $lm) {
            $path =  $this->nonCachingIliosFileSystem->getLearningMaterialTextPath($lm->relativePath);
            return [
                'id' => 'lm_' . $lm->id,
                'learningMaterialId' => $lm->id,
                'title' => $lm->title,
                'description' => $lm->description,
                'filename' => $lm->filename,
                'contents' => $this->nonCachingIliosFileSystem->getFileContents($path),
            ];
        }, array_values($materialToIndex));

        return $this->doBulkIndex(self::INDEX, $input);
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
                            'size' => 10000,
                        ],
                    ],
                ],
                'size' => 0,
            ],
        ];
        $results = $this->doSearch($params);

        return  array_column($results['aggregations']['learningMaterialId']['buckets'], 'key');
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
