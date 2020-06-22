<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\NonCachingIliosFileSystem;
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use SplFileInfo;

class LearningMaterials extends ElasticSearchBase
{
    public const INDEX = 'ilios-learning-materials';

    /**
     * @var NonCachingIliosFileSystem
     */
    private $nonCachingIliosFileSystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        NonCachingIliosFileSystem $nonCachingIliosFileSystem,
        Config $config,
        LoggerInterface $logger,
        Client $client = null
    ) {
        parent::__construct($config, $client);
        $this->nonCachingIliosFileSystem = $nonCachingIliosFileSystem;
        $this->logger = $logger;
    }

    /**
     * @param LearningMaterialDTO[] $materials
     * @return bool
     */
    public function index(array $materials): bool
    {
        foreach ($materials as $material) {
            if (!$material instanceof LearningMaterialDTO) {
                throw new InvalidArgumentException(
                    sprintf(
                        '$materials must be an array of %s. %s found',
                        LearningMaterialDTO::class,
                        get_class($material)
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
                    'data' => $data
                ];
            }

            return $materials;
        }, []);

        return $this->doBulkIndex(self::INDEX, $extractedMaterials);
    }

    protected function findByIds(array $ids): array
    {
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'ids' => [
                        'values' => $ids,
                    ]
                ],
                "_source" => ['_id']
            ]
        ];
        $results = $this->doSearch($params);
        return array_map(function (array $item) {
            return (int) $item['_id'];
        }, $results['hits']['hits']);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $result = $this->doDeleteByQuery([
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'term' => ['learningMaterialId' => $id]
                ]
            ]
        ]);

        return !count($result['failures']);
    }

    /**
     * Base64 encodes learning material contents for indexing
     * Files larger than the upload limit are ignored
     * @param LearningMaterialDTO $dto
     * @return string|null
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
                'default_pipeline' => 'learning_materials',
            ],
            'mappings' => [
                '_meta' => [
                    'version' => '1',
                ],
                'properties' => [
                    'learningMaterialId' => [
                        'type' => 'integer'
                    ],
                    'material' => [
                        'type' => 'object'
                    ],
                    'ingestTime' => [
                        'type' => 'date',
                        'format' => 'date_optional_time||basic_date_time_no_millis||epoch_second||epoch_millis'
                    ],
                ]
            ]
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
                            'field' => 'data'
                        ],
                        'set' => [
                            'field' => '_source.ingestTime',
                            'value' => '{{_ingest.timestamp}}',
                        ],
                    ]
                ]
            ]
        ];
    }
}
