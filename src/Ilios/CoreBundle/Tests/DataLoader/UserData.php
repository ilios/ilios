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
            'alerts' => [1],
            'roles' => [],
            'cohorts' => [1]
        );



        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
