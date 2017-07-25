<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * UserMadeReminder API endpoint Test.
 * @group api_2
 */
class UserMadeReminderTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'userMadeReminders';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserMadeReminderData',
            'Tests\CoreBundle\Fixture\LoadUserData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'note' => ['note', $this->getFaker()->text(150)],
            'dueDate' => ['dueDate', $this->getFaker()->iso8601, $skipped = true],
            'closed' => ['closed', false],
            'user' => ['user', 2, $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'note' => [[1], ['note' => 'second note']],
            'open' => [[1], ['closed' => false]],
            'closed' => [[0], ['closed' => true]],
            'user' => [[0, 1], ['user' => 2]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['createdAt'];
    }
}
