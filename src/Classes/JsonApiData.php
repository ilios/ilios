<?php

declare(strict_types=1);

namespace App\Classes;

use App\Normalizer\JsonApiDTONormalizer;
use App\Service\EntityManagerLookup;

class JsonApiData
{
    protected $data = [];
    protected $includes = [];
    /**
     * @var EntityManagerLookup
     */
    protected $entityManagerLookup;
    /**
     * @var JsonApiDTONormalizer
     */
    protected $normalizer;

    public function __construct(
        EntityManagerLookup $entityManagerLookup,
        JsonApiDTONormalizer $normalizer,
        array $data,
        array $sideLoadFields
    ) {
        $this->entityManagerLookup = $entityManagerLookup;
        $this->normalizer = $normalizer;
        foreach ($data as $item) {
            $shapedItem = $this->shapeItem($item);
            $this->data[] = $shapedItem;
            $this->extractSideLoadData($shapedItem['relationships'], $sideLoadFields);
        }
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

                $this->sideLoad($type, $ids, $sideLoadFields[$key]);
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

    protected function sideLoad(string $type, array $ids, array $sideLoadFields): void
    {
        $alreadyIncluded = $this->getIncludedIdsByType();
        $newIds = array_key_exists($type, $alreadyIncluded) ? array_diff($ids, $alreadyIncluded[$type]) : $ids;
        if (count($newIds)) {
            $manager = $this->entityManagerLookup->getManagerForEndpoint($type);
            $dtos = $manager->findDTOsBy(['id' => $newIds]);
            foreach ($dtos as $dto) {
                $data = $this->normalizer->normalize($dto, 'json-api');
                $shaped = $this->shapeItem($data);
                $this->includes[] = $shaped;
                $this->extractSideLoadData($shaped['relationships'], $sideLoadFields);
            }
        }
    }
}
