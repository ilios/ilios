<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * CurriculumInventoryInstitution API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CurriculumInventoryInstitutionTest extends AbstractTest
{
    protected $testName =  'curriculuminventoryinstitution';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryInstitutionData',
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'aamcCode' => ['aamcCode', $this->getFaker()->text],
            'addressStreet' => ['addressStreet', $this->getFaker()->text],
            'addressCity' => ['addressCity', $this->getFaker()->text],
            'addressStateOrProvince' => ['addressStateOrProvince', $this->getFaker()->text],
            'addressZipCode' => ['addressZipCode', $this->getFaker()->text],
            'addressCountryCode' => ['addressCountryCode', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'name' => [[0], ['filters[name]' => 'test']],
            'aamcCode' => [[0], ['filters[aamcCode]' => 'test']],
            'addressStreet' => [[0], ['filters[addressStreet]' => 'test']],
            'addressCity' => [[0], ['filters[addressCity]' => 'test']],
            'addressStateOrProvince' => [[0], ['filters[addressStateOrProvince]' => 'test']],
            'addressZipCode' => [[0], ['filters[addressZipCode]' => 'test']],
            'addressCountryCode' => [[0], ['filters[addressCountryCode]' => 'test']],
            'school' => [[0], ['filters[school]' => 'test']],
        ];
    }

}