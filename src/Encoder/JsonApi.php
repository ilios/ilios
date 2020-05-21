<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Normalizer\JsonApiDTO;
use App\Service\EntityManagerLookup;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Exception;

class JsonApi implements EncoderInterface, DecoderInterface
{
    use ContainerAwareTrait;

    protected const FORMAT = 'json-api';

    /**
     * @var EntityManagerLookup
     */
    protected $entityManagerLookup;

    /**
     * @var JsonApiDTO
     */
    protected $normalizer;

    public function __construct(EntityManagerLookup $entityManagerLookup, JsonApiDTO $normalizer)
    {

        $this->entityManagerLookup = $entityManagerLookup;
        $this->normalizer = $normalizer;
    }

    public function decode(string $data, string $format, array $context = [])
    {
        // TODO: Implement decode() method.
    }

    public function supportsDecoding(string $format)
    {
        // TODO: Implement supportsDecoding() method.
    }

    public function encode($data, string $format, array $context = [])
    {
        $shaped = $this->shapeData($data, $this->extractSideLoadFields($context));

        if (array_key_exists('singleItem', $context) && $context['singleItem']) {
            $data = $shaped['data'];
            $item = $data[0];
            $shaped['data'] = $item;
        }

        return json_encode($shaped);
    }

    protected function shapeData(array $data, array $sideLoadFields): array
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

    public function supportsEncoding(string $format)
    {
        return self::FORMAT === $format;
    }

    protected function extractSideLoadFields(array $context): array
    {
        $sideLoadFields = [];
        if (array_key_exists('include', $context) && !empty($context['include'])) {
            $fields = explode(',', $context['include']);
            $dotToTree = function (string $str) use (&$dotToTree) {

                if ($str) {
                    $parts = explode('.', $str);
                    $key = array_shift($parts);
                    return [ $key => $dotToTree(implode('.', $parts))];
                }

                return [];
            };
            $sideLoadFields = array_reduce(
                array_map($dotToTree, $fields),
                function (array $carry, array $tree) {
                    $key = array_key_first($tree);
                    if (!array_key_exists($key, $carry)) {
                        $carry[$key] = [];
                    }
                    $carry[$key] = array_merge($carry[$key], $tree[$key]);

                    return $carry;
                },
                []
            );
        }

        return $sideLoadFields;
    }
}
