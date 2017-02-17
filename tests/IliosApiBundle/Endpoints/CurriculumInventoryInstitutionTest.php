<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CurriculumInventoryInstitution API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventoryInstitutionTest extends AbstractEndpointTest
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
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'name' => [[0], ['name' => 'test']],
            'aamcCode' => [[0], ['aamcCode' => 'test']],
            'addressStreet' => [[0], ['addressStreet' => 'test']],
            'addressCity' => [[0], ['addressCity' => 'test']],
            'addressStateOrProvince' => [[0], ['addressStateOrProvince' => 'test']],
            'addressZipCode' => [[0], ['addressZipCode' => 'test']],
            'addressCountryCode' => [[0], ['addressCountryCode' => 'test']],
            'school' => [[0], ['school' => 'test']],
        ];
    }

}