<?php

namespace Tests\CoreBundle\DataLoader;

class UserData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'lastName' => $this->faker->lastName,
            'firstName' => $this->faker->firstName,
            'middleName' => $this->faker->firstName,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '1111@school.edu',
            'userSyncIgnore' => false,
            'icsFeedKey' => hash('sha256', '1'),
            'learningMaterials' => ['1', '2', '3'],
            'reports' => [],
            'school' => '1',
            'authentication' => '1',
            'primaryCohort' => '1',
            'directedCourses' => ['1'],
            'learnerGroups' => [],
            'instructedLearnerGroups' => ['1', '3'],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => ["6", "8"],
            'programYears' => ['1'],
            'alerts' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'reminders' => [],
            'pendingUserUpdates' => ['1'],
            'permissions' => [],
        );

        $arr[] = array(
            'id' => 2,
            'lastName' => 'first',
            'middleName' => $this->faker->firstName,
            'firstName' => 'first',
            'email' => 'first@example.com',
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '2222@school.edu',
            'userSyncIgnore' => true,
            'icsFeedKey' => hash('sha256', '2'),
            'learningMaterials' => [],
            'reports' => ['1', '2', '3'],
            'school' => '1',
            'authentication' => '2',
            'directedCourses' => ['2', '4'],
            'learnerGroups' => ['1', '2', '3'],
            'instructedLearnerGroups' => [],
            'instructorGroups' => ['1', '2', '3'],
            'instructorIlmSessions' => ['3'],
            'learnerIlmSessions' => ['4'],
            'offerings' => ['4'],
            'instructedOfferings' => ['5'],
            'programYears' => [],
            'alerts' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'reminders' => ['1', '2'],
            'pendingUserUpdates' => [],
            'permissions' => ['1', '2', '3']
        );

        $arr[] = array(
            'id' => 3,
            'lastName' => 'second',
            'middleName' => $this->faker->firstName,
            'firstName' => 'second',
            'email' => 'second@example.com',
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '3333@school.edu',
            'userSyncIgnore' => false,
            'icsFeedKey' => hash('sha256', '3'),
            'learningMaterials' => [],
            'reports' => [],
            'school' => "1",
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructedLearnerGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'alerts' => [],
            'roles' => ['2'],
            'cohorts' => [],
            'reminders' => [],
            'pendingUserUpdates' => [],
            'permissions' => []
        );

        $arr[] = array(
            'id' => 4,
            'lastName' => $this->faker->lastName,
            'middleName' => $this->faker->firstName,
            'firstName' => $this->faker->firstName,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '4444@school.edu',
            'userSyncIgnore' => false,
            'icsFeedKey' => hash('sha256', '4'),
            'learningMaterials' => [],
            'reports' => [],
            'school' => "2",
            'directedCourses' => ["3"],
            'learnerGroups' => [],
            'instructedLearnerGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'alerts' => [],
            'roles' => [],
            'cohorts' => [],
            'reminders' => [],
            'pendingUserUpdates' => ['2'],
            'permissions' => [],
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 5,
            'lastName' => $this->faker->lastName,
            'firstName' => $this->faker->firstName,
            'middleName' => $this->faker->firstName,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'campusId' => '5555@school.edu',
            'userSyncIgnore' => false,
            'icsFeedKey' => hash('sha256', microtime()),
            'reports' => [],
            'school' => "1",
            'directedCourses' => ['1'],
            'learnerGroups' => ['1'],
            'instructedLearnerGroups' => ['1'],
            'instructorGroups' => ['1'],
            'instructorIlmSessions' => ['1'],
            'learnerIlmSessions' => ['1'],
            'offerings' => ['1'],
            'instructedOfferings' => ['1'],
            'programYears' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'reminders' => [],
            'pendingUserUpdates' => [],
            'permissions' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
