<?php

namespace App\Tests\Endpoints;

use Doctrine\Common\Inflector\Inflector;
use App\Tests\ReadWriteEndpointTest;

/**
 * AamcPcrses API endpoint Test.
 * @group api_5
 */
class AamcPcrsTest extends ReadWriteEndpointTest
{
    protected $testName =  'aamcPcrses';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadAamcPcrsData',
            'App\Tests\Fixture\LoadCompetencyData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
            'competencies' => ['competencies', [3]],
            'id' => ['id', 'new-id', $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 'aamc-pcrs-comp-c0101']],
            'description' => [[1], ['description' => 'second description']],
            'competencies' => [[0], ['competencies' => [1]]],
        ];
    }

    public function testPostTermAamcResourceType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'aamcPcrses', 'competencies');
    }

    public function testCamelCaseInflection()
    {
        $singular = 'aamcPcrs';
        $plural = 'aamcPcrses';
        $inflectedPlural = Inflector::pluralize($singular);
        $inflectedSingular = Inflector::singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = Inflector::pluralize($plural);
        $unInflectedSingular = Inflector::singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');
    }

    public function testLowerCaseInflection()
    {
        $singular = 'aamcpcrs';
        $plural = 'aamcpcrses';
        $inflectedPlural = Inflector::pluralize($singular);
        $inflectedSingular = Inflector::singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = Inflector::pluralize($plural);
        $unInflectedSingular = Inflector::singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');
    }
}
