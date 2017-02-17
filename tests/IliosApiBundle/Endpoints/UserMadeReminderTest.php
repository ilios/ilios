<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * UserMadeReminder API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class UserMadeReminderTest extends AbstractTest
{
    protected $testName =  'usermadereminder';

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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'note' => ['note', $this->getFaker()->text],
            'dueDate' => ['dueDate', $this->getFaker()->text],
            'closed' => ['closed', false],
            'user' => ['user', $this->getFaker()->text],
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
            'createdAt' => ['createdAt', 1, 99],
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
            'note' => [[0], ['filters[note]' => 'test']],
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
            'dueDate' => [[0], ['filters[dueDate]' => 'test']],
            'closed' => [[0], ['filters[closed]' => false]],
            'user' => [[0], ['filters[user]' => 'test']],
        ];
    }

}