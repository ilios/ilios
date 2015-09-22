<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

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
            'learningMaterials' => ['1', '2', '3'],
            'publishEvents' => [],
            'reports' => [],
            'school' => '1',
            'primaryCohort' => '1',
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'alerts' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'reminders' => [],
            'pendingUserUpdates' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'lastName' => 'first',
            'middleName' => $this->faker->firstName,
            'firstName' => 'first',
            'email' => 'first@example.com',
            'phone' => $this->faker->phoneNumber,
            'enabled' => false,
            'learningMaterials' => [],
            'publishEvents' => [],
            'reports' => [],
            'school' => '1',
            'directedCourses' => ['2'],
            'learnerGroups' => ['1', '2', '3'],
            'instructorUserGroups' => [],
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
            'pendingUserUpdates' => []
        );

        $arr[] = array(
            'id' => 3,
            'lastName' => 'second',
            'middleName' => $this->faker->firstName,
            'firstName' => 'second',
            'email' => 'second@example.com',
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'learningMaterials' => [],
            'publishEvents' => [],
            'reports' => [],
            'school' => "1",
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
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
            'pendingUserUpdates' => []
        );



        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'lastName' => $this->faker->lastName,
            'firstName' => $this->faker->firstName,
            'middleName' => $this->faker->firstName,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'enabled' => true,
            'learningMaterials' => [],
            'publishEvents' => [],
            'reports' => [],
            'school' => "1",
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
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
            'pendingUserUpdates' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
