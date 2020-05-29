<?php

declare(strict_types=1);

namespace App\Service;

use App\Normalizer\JsonApiDTONormalizer;

class JsonApiDataShaper
{
    /**
     * @var EntityManagerLookup
     */
    protected $entityManagerLookup;
    /**
     * @var JsonApiDTONormalizer
     */
    protected $normalizer;

    public function __construct(EntityManagerLookup $entityManagerLookup, JsonApiDTONormalizer $normalizer)
    {
        $this->entityManagerLookup = $entityManagerLookup;
        $this->normalizer = $normalizer;
    }

    public function shapeData(array $data, array $sideLoadFields): array
    {
        $items = array_map(function (array $item) use ($sideLoadFields) {
            return $this->shapeItem($item, $sideLoadFields);
        }, $data);

        $shapedItems = array_reduce($items, function (array $carry, array $item) {
            $carry[] = $item['item'];

            return $carry;
        }, []);

        $sideLoadsByType = array_reduce($items, function (array $carry, array $item) {
            foreach ($item['sideLoad'] as $sideLoad) {
                $name = $sideLoad['name'];
                if (!array_key_exists($name, $carry)) {
                    $carry[$name] = [
                        'ids' => [],
                        'sideLoadFields' => $sideLoad['sideLoad']
                    ];
                }
                $carry[$name]['ids'][] = $sideLoad['id'];
            }

            return $carry;
        }, []);


        $includes = [];
        foreach ($sideLoadsByType as $type => $arr) {
            $sideLoads = $this->sideLoad($type, $arr['ids'], $arr['sideLoadFields']);
            $includes = array_merge($includes, $sideLoads['data'], $sideLoads['included']);
        }

        return [
            'data' => $shapedItems,
            'included' => $includes
        ];
    }

    /**
     * Flattens well structured JSON:API data into the flat array
     * our API has always used.
     */
    public function flattenJsonApiData(object $data): array
    {
        $rhett = [];
        if (property_exists($data, 'id')) {
            $manager = $this->entityManagerLookup->getManagerForEndpoint($data->type);
            $rhett[$manager->getIdField()] = $data->id;
        }
        foreach ($data->attributes as $key => $value) {
            $rhett[$key] = $value;
        }

        if (property_exists($data, 'relationships')) {
            foreach ($data->relationships as $key => $rel) {
                if (is_array($rel->data)) {
                    foreach ($rel->data as $r2) {
                        $rhett[$key][] = $r2->id;
                    }
                } else {
                    $rhett[$key] = $rel->data->id;
                }
            }
        }

        return $rhett;
    }

    protected function shapeItem(array $item, array $sideLoadFields): array
    {
        $sideLoad = [];

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
                    if (array_key_exists($name, $sideLoadFields)) {
                        $sideLoad[] = [
                            'name' => $related['type'],
                            'id' => $id,
                            'sideLoad' => $sideLoadFields[$name],
                        ];
                    }
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
                if (array_key_exists($name, $sideLoadFields)) {
                    $sideLoad[] = [
                        'name' => $related['type'],
                        'id' => $value,
                        'sideLoad' => $sideLoadFields[$name],
                    ];
                }
            }
        }

        return [
            'item' => $data,
            'sideLoad' => $sideLoad
        ];
    }

    protected function sideLoad(string $type, array $ids, array $sideLoadFields): array
    {
        $manager = $this->entityManagerLookup->getManagerForEndpoint($type);
        $dtos = $manager->findDTOsBy(['id' => $ids]);
        $data = [];
        foreach ($dtos as $dto) {
            $data[] = $this->normalizer->normalize($dto, 'json-api');
        }
        return $this->shapeData($data, $sideLoadFields);
    }
}
