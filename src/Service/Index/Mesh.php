<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Exception;
use InvalidArgumentException;

class Mesh extends OpenSearchBase
{
    public const string INDEX = 'ilios-mesh';

    public function idsQuery(string $query): array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => "*{$query}*",
                    ],
                ],
                "_source" => [
                    '_id',
                ],
            ],
        ];
        $results = $this->doSearch($params);
        return array_map(fn(array $arr) => $arr['_id'], $results['hits']['hits']);
    }

    public function index(array $descriptors): bool
    {
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof Descriptor) {
                throw new InvalidArgumentException(
                    sprintf(
                        '$descriptors must be an array of %s. %s found',
                        Descriptor::class,
                        $descriptor::class
                    )
                );
            }
        }

        $input = array_map(function (Descriptor $descriptor) {
            $conceptMap = array_reduce($descriptor->getConcepts(), function (array $carry, Concept $concept) {
                $carry['conceptNames'][] = $concept->getName();
                $carry['scopeNotes'][] = $concept->getScopeNote();
                $carry['casn1Names'][] = $concept->getCasn1Name();
                foreach ($concept->getTerms() as $term) {
                    $carry['termNames'][] = $term->getName();
                }

                return $carry;
            }, [
                'conceptNames' => [],
                'termNames' => [],
                'scopeNotes' => [],
                'casn1Names' => [],
            ]);

            return [
                'id' => $descriptor->getUi(),
                'name' => $descriptor->getName(),
                'annotation' => $descriptor->getAnnotation(),
                'previousIndexing' => join(' ', $descriptor->getPreviousIndexing()),
                'terms' => join(' ', $conceptMap['termNames']),
                'concepts' => join(' ', $conceptMap['conceptNames']),
                'scopeNotes' => join(' ', $conceptMap['scopeNotes']),
                'casn1Names' => join(' ', $conceptMap['casn1Names']),
            ];
        }, $descriptors);

        $result = $this->doBulkIndex(self::INDEX, $input);
        return !$result['errors'];
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
            ],
        ];
    }
}
