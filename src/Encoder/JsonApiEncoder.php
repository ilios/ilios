<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Service\JsonApiDataShaper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class JsonApiEncoder implements EncoderInterface, DecoderInterface
{
    use ContainerAwareTrait;

    protected const FORMAT = 'json-api';

    /**
     * @var JsonApiDataShaper
     */
    protected $dataShaper;

    public function __construct(JsonApiDataShaper $dataShaper)
    {
        $this->dataShaper = $dataShaper;
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
        $shaped = $this->dataShaper->shapeData($data, $context['sideLoadFields']);

        if (array_key_exists('singleItem', $context) && $context['singleItem']) {
            $data = $shaped['data'];
            $item = $data[0];
            $shaped['data'] = $item;
        }

        return json_encode($shaped);
    }

    public function supportsEncoding(string $format)
    {
        return self::FORMAT === $format;
    }
}
