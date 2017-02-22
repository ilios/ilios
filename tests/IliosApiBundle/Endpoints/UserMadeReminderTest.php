<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * UserMadeReminder API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class UserMadeReminderTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'usermadereminders';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserMadeReminderData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'note' => ['note', $this->getFaker()->text],
            'dueDate' => ['dueDate', $this->getFaker()->iso8601],
            'closed' => ['closed', false],
            'user' => ['user', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'createdAt' => ['createdAt', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'note' => [[0], ['note' => 'test']],
            'createdAt' => [[0], ['createdAt' => 'test']],
            'dueDate' => [[0], ['dueDate' => 'test']],
            'closed' => [[0], ['closed' => false]],
            'user' => [[0], ['user' => 'test']],
        ];
    }
}
