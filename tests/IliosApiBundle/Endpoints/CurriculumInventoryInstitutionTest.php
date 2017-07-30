<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\CoreBundle\DataLoader\SchoolData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventoryInstitution API endpoint Test.
 * @group api_2
 */
class CurriculumInventoryInstitutionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculumInventoryInstitutions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryInstitutionData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text(100)],
            'aamcCode' => ['aamcCode', $this->getFaker()->text(10)],
            'addressStreet' => ['addressStreet', $this->getFaker()->text(10)],
            'addressCity' => ['addressCity', $this->getFaker()->text(100)],
            'addressStateOrProvince' => ['addressStateOrProvince', $this->getFaker()->text(50)],
            'addressZipCode' => ['addressZipCode', $this->getFaker()->text(10)],
            'addressCountryCode' => ['addressCountryCode', $this->getFaker()->word],
            'school' => ['school', 3],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'name' => [[1], ['name' => 'second institution']],
            'aamcCode' => [[1], ['aamcCode' => 14]],
            'addressStreet' => [[1], ['addressStreet' => '221 East']],
            'addressCity' => [[0], ['addressCity' => 'first city']],
            'addressStateOrProvince' => [[1], ['addressStateOrProvince' => 'CA']],
            'addressZipCode' => [[1], ['addressZipCode' => '90210']],
            'addressCountryCode' => [[1], ['addressCountryCode' => 'BC']],
            'school' => [[1], ['school' => 2]],
        ];
    }


    /**
     * We need to create additional schools to
     * go with each new CI institution
     * @inheritdoc
     */
    public function testPostMany()
    {
        $this->markTestSkipped(
            'In order to write these we need write permissions in the school.'.
            'This seems like too much of a pain to test this right now.'
        );
        $count = 26;
        $schoolDataLoader = $this->container->get(SchoolData::class);
        $schools = $schoolDataLoader->createMany($count);
        $savedSchools = $this->postMany('schools', 'schools', $schools);

        $dataLoader = $this->getDataLoader();

        $data = array_map(function (array $school) use ($dataLoader) {
            $arr = $dataLoader->create();
            $arr['school'] = (string) $school['id'];

            return $arr;
        }, $savedSchools);

        $this->postManyTest($data);
    }
}
