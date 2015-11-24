<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SchoolData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [1],
            'competencies' => ['1', '2', '3'],
            'courses' => ["1", "2"],
            'programs' => ['1', '2'],
            'departments' => ['1', '2'],
            'topics' => ['1', '2', '3'],
            'instructorGroups' => [],
            'curriculumInventoryInstitution' => "1",
            'sessionTypes' => ['1', '2'],
            'stewards' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [],
            'competencies' => [],
            'courses' => ["3", "4"],
            'programs' => [],
            'departments' => [],
            'topics' => [],
            'instructorGroups' => [],
            'sessionTypes' => [],
            'stewards' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'departments' => [],
            'topics' => [],
            'instructorGroups' => [],
            'sessionTypes' => [],
            'stewards' => []
        );
    }

    public function createInvalid()
    {
        return [
            'id' => 'lkjdsf'
        ];
    }
}
