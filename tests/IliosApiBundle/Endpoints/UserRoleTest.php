<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * UserRole API endpoint Test.
 * @group api_4
 */
class UserRoleTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'userRoles';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserRoleData',
            'Tests\CoreBundle\Fixture\LoadUserData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', 'Developer'],
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
            'title' => [[1], ['title' => 'Something Else']],
        ];
    }

    /**
     * We can't change the title on the Developer role
     * Doing that leaves us without the permissions to change the title
     * @inheritdoc
     * @dataProvider putsToTest
     */
    public function testPut($key, $value)
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();

        //extract the ID before changing anything in case
        // the key we are changing is the ID
        $id = $data['id'];
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $id);
    }
}
