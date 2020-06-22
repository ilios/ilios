<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;
use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\NonCachingIliosFileSystem;
use OpenSearch\Client;
use InvalidArgumentException;
use SplFileInfo;

class LearningMaterials extends OpenSearchBase
{
    public const INDEX = 'ilios-learning-materials';

    public function __construct(
        private NonCachingIliosFileSystem $nonCachingIliosFileSystem,
        Config $config,
        ?Client $client = null
    ) {
        parent::__construct($config, $client);
    }

    /**
     * @param LearningMaterialDTO[] $materials
     */
    public function index(array $materials): bool
    {
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

        $existingMaterialIds = $this->findByIds(array_column($materials, 'id'));

        // The contents of LMs don't change so we shouldn't index them twice
        $newMaterials = array_filter($materials, function (LearningMaterialDTO $lm) use ($existingMaterialIds) {
            return !in_array($lm->id, $existingMaterialIds);
        });

        $extractedMaterials = array_reduce($newMaterials, function ($materials, LearningMaterialDTO $lm) {
            $data = $this->extractLearningMaterialData($lm);
            if ($data) {
                $materials[] = [
                    'id' => $lm->id,
                    'data' => $data,
                ];
            }

            return $materials;
        }, []);

        $results = array_map(function (array $arr) {
            $params = [
                'index' => self::INDEX,
                'pipeline' => 'learning_materials',
                'id' => $arr['id'],
                'body' => [
                    'learningMaterialId' => $arr['id'],
                    'data' => $arr['data'],
                ],
            ];
            return $this->client->index($params);
        }, $extractedMaterials);

        $errors = array_filter($results, fn($result) => $result['result'] === 'error');

        return empty($errors);
    }

    protected function findByIds(array $ids): array
    {
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'ids' => [
                        'values' => $ids,
                    ],
                ],
                "_source" => ['_id'],
            ],
        ];
        $results = $this->doSearch($params);
        return array_map(function (array $item) {
            return (int) $item['_id'];
        }, $results['hits']['hits']);
    }

    /**
     * @param int $id
     */
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

    /**
     * Base64 encodes learning material contents for indexing
     * Files larger than the upload limit are ignored
     */
    protected function extractLearningMaterialData(LearningMaterialDTO $dto): ?string
    {
        //skip files without useful text content
        if ($dto->mimetype && preg_match('/image|video|audio/', $dto->mimetype)) {
            return null;
        }
        $info = new SplFileInfo($dto->filename);
        $extension = $info->getExtension();
        if (in_array($extension, ['mp3', 'mov'])) {
            return null;
        }

        $data = $this->nonCachingIliosFileSystem->getFileContents($dto->relativePath);
        $encodedData = base64_encode($data);
        if (strlen($encodedData) < $this->uploadLimit) {
            return $encodedData;
        }

        return null;
    }

    public static function getMapping(): array
    {
        return [
            'settings' => [
                'number_of_shards' => 5,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                '_meta' => [
                    'version' => '1',
                ],
                'properties' => [
                    'learningMaterialId' => [
                        'type' => 'integer',
                    ],
                    'material' => [
                        'type' => 'object',
                    ],
                ],
            ],
        ];
    }

    public static function getPipeline(): array
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
                        ],
                        'remove' => [
                            'field' => 'data',
                        ],
                        'set' => [
                            'field' => '_source.ingest_time',
                            'value' => '{{_ingest.timestamp}}',
                        ],
                    ],
                ],
            ],
        ];
    }
}
