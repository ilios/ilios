<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\ElasticSearchBase;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Exception;

class Mesh extends ElasticSearchBase
{
    public const INDEX = 'ilios-mesh';

    /**
     * @param string $query
     * @return array
     * @throws Exception when search is not configured
     */
    public function idsQuery(string $query)
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params = [
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => "*${query}*",
                    ]
                ],
                "_source" => [
                    '_id'
                ]
            ]
        ];
        $results = $this->doSearch($params);
        return array_map(function (array $arr) {
            return $arr['_id'];
        }, $results['hits']['hits']);
    }

    /**
     * @param Descriptor[] $descriptors
     * @return bool
     */
    public function index(array $descriptors): bool
    {
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof Descriptor) {
                throw new \InvalidArgumentException(
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
