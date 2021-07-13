<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\NonCachingIliosFileSystem;
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\FpdiException;
use setasign\Fpdi\PdfParser\StreamReader;
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
                        $material::class
                    )
                );
            }
        }

        $extractedMaterials = array_reduce($materials, function ($materials, LearningMaterialDTO $lm) {
            foreach ($this->extractLearningMaterialData($lm) as $data) {
                $materials[] = [
                    'id' => $lm->id,
                    'data' => $data
                ];
            }
            return $materials;
        }, []);

        $results = array_map(function (array $arr) {
            $params = [
                'index' => self::INDEX,
                'pipeline' => 'learning_materials',
                'body' => [
                    'learningMaterialId' => $arr['id'],
                    'data' => $arr['data'],
                ]
            ];
            return $this->client->index($params);
        }, $extractedMaterials);

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
     *
     * @param LearningMaterialDTO $dto
     * @return array
     */
    protected function extractLearningMaterialData(LearningMaterialDTO $dto): array
    {
        //skip files without useful text content
        if ($dto->mimetype && preg_match('/image|video|audio/', $dto->mimetype)) {
            return [];
        }
        $info = new SplFileInfo($dto->filename);
        $extension = $info->getExtension();
        if (in_array($extension, ['mp3', 'mov'])) {
            return [];
        }

        $data = $this->nonCachingIliosFileSystem->getFileContents($dto->relativePath);
        $encodedData = base64_encode($data);
        if (strlen($encodedData) < $this->uploadLimit) {
            return [
                $encodedData
            ];
        }

        return [];
    }

    public static function getMapping(): array
    {
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
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
                        ]
                    ]
                ]
            ]
        ];
    }
}
