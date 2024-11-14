<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadMeshConceptData;
use App\Tests\Fixture\LoadMeshTermData;

/**
 * MeshTerm API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_3')]
class MeshTermTest extends AbstractMeshEndpoint
{
    protected string $testName =  'meshTerms';

    protected function getFixtures(): array
    {
        return [
            LoadMeshTermData::class,
            LoadMeshConceptData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'meshTermUid' => [[1], ['meshTermUid' => 'uid2']],
            'name' => [[1], ['name' => 'second term']],
            'lexicalTag' => [[0], ['lexicalTag' => 'first tag']],
            'conceptPreferred' => [[0], ['conceptPreferred' => true]],
            'conceptNotPreferred' => [[1], ['conceptPreferred' => false]],
            'recordPreferred' => [[1], ['recordPreferred' => true]],
            'recordNotPreferred' => [[0], ['recordPreferred' => false]],
            'permuted' => [[0], ['permuted' => true]],
            'notPermuted' => [[1], ['permuted' => false]],
            'concepts' => [[0, 1], ['concepts' => [1]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }

    public function getTimeStampFields(): array
    {
        return ['createdAt', 'updatedAt'];
    }
}
