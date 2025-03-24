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
use Psr\Log\LoggerInterface;

class LearningMaterials extends OpenSearchBase
{
    public const string INDEX = 'ilios-learning-materials';

    public function __construct(
        private NonCachingIliosFileSystem $nonCachingIliosFileSystem,
        protected LoggerInterface $logger,
        Config $config,
        ?Client $client = null
    ) {
        parent::__construct($config, $client);
    }

    public function index(array $materials): bool
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

        $input = array_map(function (LearningMaterialDTO $lm) {
            $path =  $this->nonCachingIliosFileSystem->getLearningMaterialTextPath($lm->relativePath);
            return [
                'id' => 'lm_' . $lm->id,
                'learningMaterialId' => $lm->id,
                'data' => $this->nonCachingIliosFileSystem->getFileContents($path),
            ];
        }, $materials);

        return $this->doBulkIndex(self::INDEX, $input);
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
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
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
            ],
        ];
    }
}
