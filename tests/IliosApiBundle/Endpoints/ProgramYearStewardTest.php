<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\CoreBundle\DataLoader\DepartmentData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * ProgramYearSteward API endpoint Test.
 * @group api_2
 */
class ProgramYearStewardTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'programYearStewards';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
            'Tests\CoreBundle\Fixture\LoadDepartmentData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadSchoolData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'department' => ['department', 3],
            'programYear' => ['programYear', 2],
            'school' => ['school', 2],
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
            'department' => [[1], ['department' => 2]],
            'programYear' => [[0, 1], ['programYear' => 1]],
            'school' => [[0, 1], ['school' => 1]],
        ];
    }

    /**
     * Creating many runs into UNIQUE constraints quick
     * so instead build a bunch of new departments to use
     */
    public function testPostMany()
    {
        $departmentDataLoader = $this->container->get(DepartmentData::class);
        $departments = $departmentDataLoader->createMany(51);
        $savedDepartments = $this->postMany('departments', 'departments', $departments);

        $dataLoader = $this->getDataLoader();

        $data = [];
        foreach ($savedDepartments as $i => $department) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['department'] = (string) $department['id'];

            $data[] = $arr;
        }


        $this->postManyTest($data);
    }

    /**
     * Override this so we don't change any values.  Changing something
     * like department or school causes key conflicts and there isn't really
     * anything non-unique to change here.
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $data) {
            $this->putTest($data, $data, $data['id']);
        }
    }
}
