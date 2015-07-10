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
            'directedCourses' => [],
            'learnerGroups' => [],
            'instructorUserGroups' => [],
            'instructorGroups' => ['1'],
            'instructorIlmSessions' => [],
            'learnerIlmSessions' => [],
            'offerings' => [],
            'instructedOfferings' => [],
            'programYears' => [],
            'instructionHours' => [],
            'alerts' => ['1'],
            'roles' => ['1'],
            'cohorts' => ['1']
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
            'cohorts' => []
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
            'cohorts' => []
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
            'cohorts' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
