<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Classes\Inflector;
use App\Service\InflectorFactory;
use App\Tests\Fixture\LoadAamcPcrsData;
use App\Tests\Fixture\LoadCompetencyData;
use App\Tests\ReadWriteEndpointTestCase;

/**
 * AamcPcrses API endpoint Test.
 * @group api_5
 */
class AamcPcrsTest extends ReadWriteEndpointTestCase
{
    protected string $testName =  'aamcPcrses';

    protected function getFixtures(): array
    {
        return [
            LoadAamcPcrsData::class,
            LoadCompetencyData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'description' => ['description', 'lorem ipsum'],
            'competencies' => ['competencies', [3]],
            'id' => ['id', 'new-id', $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 'aamc-pcrs-comp-c0101']],
            'description' => [[1], ['description' => 'second description']],
            'competencies' => [[0], ['competencies' => [1]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }

    public function testPostTermAamcResourceType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'aamcPcrses', 'competencies');
    }

    /**
     * @group twice
     */
    public function testInflection()
    {
        $singular = 'aamcPcrs';
        $plural = 'aamcPcrses';
        $inflector = InflectorFactory::create();
        $inflectedPlural = $inflector->pluralize($singular);
        $inflectedSingular = $inflector->singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = $inflector->pluralize($plural);
        $unInflectedSingular = $inflector->singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');

        $camelPlural = $inflector->camelize('AamcPcrses');
        $this->assertSame($camelPlural, 'aamcPcrses');

        $camelSingular = $inflector->camelize('AamcPcrs');
        $this->assertSame($camelSingular, 'aamcPcrs');
    }

    /**
     * @group twice
     */
    public function testLowerCaseInflection()
    {
        $singular = 'aamcpcrs';
        $plural = 'aamcpcrses';
        $inflector = InflectorFactory::create();
        $inflectedPlural = $inflector->pluralize($singular);
        $inflectedSingular = $inflector->singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = $inflector->pluralize($plural);
        $unInflectedSingular = $inflector->singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');

        $camelPlural = $inflector->camelize('aamcpcrses');
        $this->assertSame($camelPlural, 'aamcpcrses');

        $camelSingular = $inflector->camelize('aamcpcrs');
        $this->assertSame($camelSingular, 'aamcpcrs');
    }

    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        parent::markTestSkipped('Skipped');
    }
}
