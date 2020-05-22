<?php

declare(strict_types=1);

namespace App\Encoder;

use App\Service\JsonApiDataShaper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class JsonApi implements EncoderInterface, DecoderInterface
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
        $shaped = $this->dataShaper->shapeData($data, $this->extractSideLoadFields($context));

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
