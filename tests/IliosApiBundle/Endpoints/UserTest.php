<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * User API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class UserTest extends AbstractEndpointTest
{
    protected $testName =  'users';

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
            'lastName' => [[0], ['lastName' => 'test']],
            'firstName' => [[0], ['firstName' => 'test']],
            'middleName' => [[0], ['middleName' => 'test']],
            'phone' => [[0], ['phone' => 'test']],
            'email' => [[0], ['email' => 'test']],
            'enabled' => [[0], ['enabled' => false]],
            'campusId' => [[0], ['campusId' => 'test']],
            'otherId' => [[0], ['otherId' => 'test']],
            'userSyncIgnore' => [[0], ['userSyncIgnore' => false]],
            'icsFeedKey' => [[0], ['icsFeedKey' => 'test']],
            'authentication' => [[0], ['authentication' => 'test']],
            'reminders' => [[0], ['reminders' => [1]]],
            'reports' => [[0], ['reports' => [1]]],
            'school' => [[0], ['school' => 'test']],
            'directedCourses' => [[0], ['directedCourses' => [1]]],
            'administeredCourses' => [[0], ['administeredCourses' => [1]]],
            'administeredSessions' => [[0], ['administeredSessions' => [1]]],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'instructedLearnerGroups' => [[0], ['instructedLearnerGroups' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'instructorIlmSessions' => [[0], ['instructorIlmSessions' => [1]]],
            'learnerIlmSessions' => [[0], ['learnerIlmSessions' => [1]]],
            'offerings' => [[0], ['offerings' => [1]]],
            'instructedOfferings' => [[0], ['instructedOfferings' => [1]]],
            'programYears' => [[0], ['programYears' => [1]]],
            'roles' => [[0], ['roles' => [1]]],
            'cohorts' => [[0], ['cohorts' => [1]]],
            'primaryCohort' => [[0], ['primaryCohort' => 'test']],
            'pendingUserUpdates' => [[0], ['pendingUserUpdates' => [1]]],
            'permissions' => [[0], ['permissions' => [1]]],
            'directedSchools' => [[0], ['directedSchools' => [1]]],
            'administeredSchools' => [[0], ['administeredSchools' => [1]]],
            'directedPrograms' => [[0], ['directedPrograms' => [1]]],
            'root' => [[0], ['root' => false]],
        ];
    }

}