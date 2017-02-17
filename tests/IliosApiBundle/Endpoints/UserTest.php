<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * User API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class UserTest extends AbstractTest
{
    protected $testName =  'user';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserData',
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
            'lastName' => ['lastName', $this->getFaker()->text],
            'firstName' => ['firstName', $this->getFaker()->text],
            'middleName' => ['middleName', $this->getFaker()->text],
            'phone' => ['phone', $this->getFaker()->text],
            'email' => ['email', $this->getFaker()->text],
            'enabled' => ['enabled', false],
            'campusId' => ['campusId', $this->getFaker()->text],
            'otherId' => ['otherId', $this->getFaker()->text],
            'userSyncIgnore' => ['userSyncIgnore', false],
            'icsFeedKey' => ['icsFeedKey', $this->getFaker()->text],
            'authentication' => ['authentication', $this->getFaker()->text],
            'reminders' => ['reminders', [1]],
            'reports' => ['reports', [1]],
            'school' => ['school', $this->getFaker()->text],
            'directedCourses' => ['directedCourses', [1]],
            'administeredCourses' => ['administeredCourses', [1]],
            'administeredSessions' => ['administeredSessions', [1]],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructedLearnerGroups' => ['instructedLearnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'instructorIlmSessions' => ['instructorIlmSessions', [1]],
            'learnerIlmSessions' => ['learnerIlmSessions', [1]],
            'offerings' => ['offerings', [1]],
            'instructedOfferings' => ['instructedOfferings', [1]],
            'programYears' => ['programYears', [1]],
            'roles' => ['roles', [1]],
            'cohorts' => ['cohorts', [1]],
            'primaryCohort' => ['primaryCohort', $this->getFaker()->text],
            'pendingUserUpdates' => ['pendingUserUpdates', [1]],
            'permissions' => ['permissions', [1]],
            'directedSchools' => ['directedSchools', [1]],
            'administeredSchools' => ['administeredSchools', [1]],
            'directedPrograms' => ['directedPrograms', [1]],
            'root' => ['root', false],
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
            'lastName' => [[0], ['filters[lastName]' => 'test']],
            'firstName' => [[0], ['filters[firstName]' => 'test']],
            'middleName' => [[0], ['filters[middleName]' => 'test']],
            'phone' => [[0], ['filters[phone]' => 'test']],
            'email' => [[0], ['filters[email]' => 'test']],
            'enabled' => [[0], ['filters[enabled]' => false]],
            'campusId' => [[0], ['filters[campusId]' => 'test']],
            'otherId' => [[0], ['filters[otherId]' => 'test']],
            'userSyncIgnore' => [[0], ['filters[userSyncIgnore]' => false]],
            'icsFeedKey' => [[0], ['filters[icsFeedKey]' => 'test']],
            'authentication' => [[0], ['filters[authentication]' => 'test']],
            'reminders' => [[0], ['filters[reminders]' => [1]]],
            'reports' => [[0], ['filters[reports]' => [1]]],
            'school' => [[0], ['filters[school]' => 'test']],
            'directedCourses' => [[0], ['filters[directedCourses]' => [1]]],
            'administeredCourses' => [[0], ['filters[administeredCourses]' => [1]]],
            'administeredSessions' => [[0], ['filters[administeredSessions]' => [1]]],
            'learnerGroups' => [[0], ['filters[learnerGroups]' => [1]]],
            'instructedLearnerGroups' => [[0], ['filters[instructedLearnerGroups]' => [1]]],
            'instructorGroups' => [[0], ['filters[instructorGroups]' => [1]]],
            'instructorIlmSessions' => [[0], ['filters[instructorIlmSessions]' => [1]]],
            'learnerIlmSessions' => [[0], ['filters[learnerIlmSessions]' => [1]]],
            'offerings' => [[0], ['filters[offerings]' => [1]]],
            'instructedOfferings' => [[0], ['filters[instructedOfferings]' => [1]]],
            'programYears' => [[0], ['filters[programYears]' => [1]]],
            'roles' => [[0], ['filters[roles]' => [1]]],
            'cohorts' => [[0], ['filters[cohorts]' => [1]]],
            'primaryCohort' => [[0], ['filters[primaryCohort]' => 'test']],
            'pendingUserUpdates' => [[0], ['filters[pendingUserUpdates]' => [1]]],
            'permissions' => [[0], ['filters[permissions]' => [1]]],
            'directedSchools' => [[0], ['filters[directedSchools]' => [1]]],
            'administeredSchools' => [[0], ['filters[administeredSchools]' => [1]]],
            'directedPrograms' => [[0], ['filters[directedPrograms]' => [1]]],
            'root' => [[0], ['filters[root]' => false]],
        ];
    }

}