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
            'learningMaterials' => ['1', '2'],
            'publishEvents' => [],
            'reports' => [],
            'primarySchool' => '1',
            'primaryCohort' => '1',
            'directedCourses' => ['2'],
            'learnerGroups' => ['1', '2', '3'],
            'instructorUserGroups' => [],
            'instructorGroups' => ['1', '2', '3'],
            'instructorIlmSessions' => ['3'],
            'learnerIlmSessions' => ['4'],
            'offerings' => ['4'],
            'instructedOfferings' => ['5'],
            'programYears' => [],
            'instructionHours' => [],
            'alerts' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1'],
            'reminders' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'lastName' => 'first',
            'middleName' => $this->faker->firstName,
            'firstName' => 'first',
            'email' => 'first@example.com',
            'learningMaterials' => [],
            'publishEvents' => [],
            'reports' => [],
            'primarySchool' => '1',
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'instructionHours' => [],
            'alerts' => ['1'],
            'roles' => ['1'],
            'cohorts' => [],
            'reminders' => []
        );

        $arr[] = array(
            'id' => 3,
            'lastName' => 'second',
            'middleName' => $this->faker->firstName,
            'firstName' => 'second',
            'email' => 'second@example.com',
            'learningMaterials' => [],
            'publishEvents' => [],
            'reports' => [],
            'primarySchool' => "1",
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'instructionHours' => [],
            'alerts' => [],
            'roles' => [],
            'cohorts' => [],
            'reminders' => []
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
            'learningMaterials' => [],
            'publishEvents' => [],
            'reports' => [],
            'primarySchool' => "1",
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
            'instructorGroups' => [],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'instructionHours' => [],
            'alerts' => [],
            'roles' => [],
            'cohorts' => [],
            'reminders' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
