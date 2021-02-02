<?php

declare(strict_types=1);

namespace App\Classes;

use App\Service\EntityRepositoryLookup;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonApiData
{
    protected array $data = [];
    protected array $includes = [];
    protected array $sideLoadCandidates = [];
    protected EntityRepositoryLookup $entityRepositoryLookup;
    protected NormalizerInterface $normalizer;

    public function __construct(
        EntityRepositoryLookup $entityRepositoryLookup,
        NormalizerInterface $normalizer,
        array $data,
        array $sideLoadFields
    ) {
        $this->entityRepositoryLookup = $entityRepositoryLookup;
        $this->normalizer = $normalizer;
        foreach ($data as $item) {
            $shapedItem = $this->shapeItem($item);
            $this->data[] = $shapedItem;
            $this->extractSideLoadData($shapedItem['relationships'], $sideLoadFields);
        }
        $this->executeSideLoad();
    }

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'included' => $this->includes
        ];
    }

    protected function shapeItem(array $item): array
    {
        $data = [
            'id' => (string) $item['id'],
            'type' => $item['type'],
            'attributes' => $item['attributes'],
            'relationships' => [],
        ];
        foreach ($item['related'] as $name => $related) {
            $relatedData = [];
            $value = $related['value'];
            if (is_array($value)) {
                foreach ($value as $id) {
                    $relatedData[] = [
                        'type' => $related['type'],
                        'id' => (string) $id
                    ];
                }
                $data['relationships'][$name] = [
                    'data' => $relatedData
                ];
            } else {
                $data['relationships'][$name] = [
                    'data' => [
                        'type' => $related['type'],
                        'id' => (string) $value
                    ]
                ];
            }
        }

        return $data;
    }

    protected function getTypeForData(array $data): string
    {
        if (array_key_exists('type', $data)) {
            return $data['type'];
        }
        return $this->getTypeForData($data[0]);
    }

    protected function getIdsForData(array $data): array
    {
        if (array_key_exists('id', $data)) {
            return [$data['id']];
        }
        return array_map(function ($item) {
            return $item['id'];
        }, $data);
    }

    protected function extractSideLoadData(array $relationships, array $sideLoadFields): void
    {
        $keys = array_keys($sideLoadFields);
        foreach ($keys as $key) {
            if (array_key_exists($key, $relationships)) {
                $r = $relationships[$key];
                $type = $this->getTypeForData($r['data']);
                $ids = $this->getIdsForData($r['data']);

                $this->prepareSideLoad($type, $ids, $sideLoadFields[$key]);
            }
        }
    }

    protected function getIncludedIdsByType(): array
    {
        return array_reduce($this->includes, function (array $carry, array $item) {
            $t = $item['type'];
            if (!array_key_exists($t, $carry)) {
                $carry[$t] = [];
            }
            $carry[$t][] = $item['id'];

            return $carry;
        }, []);
    }

    protected function getIncluded(string $id, string $type): ?array
    {
        foreach ($this->includes as $item) {
            if ($item['type'] === $type && $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    protected function prepareSideLoad(string $type, array $ids, array $sideLoadFields): void
    {
        if (!array_key_exists($type, $this->sideLoadCandidates)) {
            $this->sideLoadCandidates[$type] = [];
        }
        foreach ($ids as $id) {
            if (!array_key_exists($id, $this->sideLoadCandidates[$type])) {
                $this->sideLoadCandidates[$type][$id] = [];
            }
            $this->sideLoadCandidates[$type][$id][] = $sideLoadFields;
        }
    }

    /**
     * Execute a batch of side loaded data
     * By batching up each layer of data we save significant numbers of database queries
     * and make this process much much faster. This method takes a batch and includes it in the final
     * output
     */
    protected function executeSideLoad(): void
    {
        $sideLoadCandidates = $this->sideLoadCandidates;
        $this->sideLoadCandidates = [];
        foreach ($sideLoadCandidates as $type => $candidates) {
            $ids = array_keys($candidates);
            $alreadyIncluded = $this->getIncludedIdsByType();
            $newIds = array_key_exists($type, $alreadyIncluded) ? array_diff($ids, $alreadyIncluded[$type]) : $ids;
            if (count($newIds)) {
                $manager = $this->entityRepositoryLookup->getRepositoryForEndpoint($type);
                $dtos = $manager->findDTOsBy(['id' => $newIds]);
                foreach ($dtos as $dto) {
                    $data = $this->normalizer->normalize($dto, 'json-api');
                    $shaped = $this->shapeItem($data);
                    $this->includes[] = $shaped;
                }
            }
            foreach ($ids as $id) {
                $item = $this->getIncluded((string) $id, $type);
                foreach ($candidates[$id] as $sideLoadFields) {
                    $this->extractSideLoadData($item['relationships'], $sideLoadFields);
                }
            }
        }
        if (count($this->sideLoadCandidates)) {
            $this->executeSideLoad();
        }
    }
}
