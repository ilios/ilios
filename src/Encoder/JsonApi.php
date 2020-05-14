<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Entity\Manager\ManagerInterface;
use App\Service\EndpointResponseNamer;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Exception;

class JsonApi implements EncoderInterface, DecoderInterface
{
    use ContainerAwareTrait;

    protected const FORMAT = 'json-api';

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    public function __construct(EndpointResponseNamer $endpointResponseNamer)
    {
        $this->endpointResponseNamer = $endpointResponseNamer;
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
        $rhett = [
            'data' => [],
            'included' => []
        ];

        foreach ($data as $object) {
            $arr = [
                'id' => (string) $object['id'],
                'type' => $object['type'],
                'attributes' => $object['attributes'],
                'relationships' => [],
            ];
            foreach ($object['included'] as $inc) {
                $rhett['included'][] = $inc;
            }
            foreach ($object['related'] as $name => $related) {
                $relatedData = [];
                $value = $related['value'];
                if (is_array($value)) {
                    foreach ($value as $id) {
                        $relatedData[] = [
                            'type' => $related['type'],
                            'id' => (string) $id
                        ];
                    }
                    $arr['relationships'][$name] = [
                        'data' => $relatedData
                    ];
                } else {
                    $arr['relationships'][$name] = [
                        'data' => [
                            'type' => $related['type'],
                            'id' => (string) $value
                        ]
                    ];
                }
            }

            $rhett['data'][] = $arr;
        }

        return json_encode($rhett);
    }

    public function supportsEncoding(string $format)
    {
        return self::FORMAT === $format;
    }
}
