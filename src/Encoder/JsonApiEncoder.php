<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Service\JsonApiDataShaper;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class JsonApiEncoder implements EncoderInterface, DecoderInterface
{
    protected const string FORMAT = 'json-api';

    public function __construct(protected JsonApiDataShaper $dataShaper)
    {
    }

    public function decode(string $data, string $format, array $context = []): mixed
    {
        $obj = json_decode($data);
        $rhett = [];
        if (is_array($obj->data)) {
            foreach ($obj->data as $o) {
                $rhett[] = $this->dataShaper->flattenJsonApiData($o);
            }
        } else {
            $rhett[] = $this->dataShaper->flattenJsonApiData($obj->data);
        }

        return $rhett;
    }

    public function supportsDecoding(string $format, array $context = []): bool
    {
        return self::FORMAT === $format;
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        $shaped = $this->dataShaper->shapeData($data, $context['sideLoadFields']);

        if (array_key_exists('singleItem', $context) && $context['singleItem']) {
            $shaped['data'] = $shaped['data'][0] ?? null;
        }

        return json_encode($shaped);
    }

    public function supportsEncoding(string $format, array $context = []): bool
    {
        return self::FORMAT === $format;
    }
}
