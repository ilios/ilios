<?php

namespace Tests\CoreBundle\DataLoader;

class SchoolData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => '1' . $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [1],
            'competencies' => ['1', '2', '3'],
            'courses' => ["1", "2"],
            'programs' => ['1', '2'],
            'departments' => ['1', '2'],
            'instructorGroups' => ['1', '2', '3'],
            'curriculumInventoryInstitution' => "1",
            'sessionTypes' => ['1', '2'],
            'stewards' => ['1', '2'],
            'vocabularies' => ['1'],
            'directors' => ['1'],
            'administrators' => ['1'],
        );

        $arr[] = array(
            'id' => 2,
            'title' => '2' . $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [],
            'competencies' => [],
            'courses' => ["3", "4"],
            'programs' => ["3"],
            'departments' => [],
            'instructorGroups' => ['4'],
            'curriculumInventoryInstitution' => "2",
            'sessionTypes' => [],
            'stewards' => [],
            'vocabularies' => ['2'],
            'directors' => [],
            'administrators' => [],
        );

        $arr[] = array(
            'id' => 3,
            'title' => '3' . $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [],
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'departments' => [],
            'instructorGroups' => [],
            'sessionTypes' => [],
            'stewards' => [],
            'vocabularies' => [],
            'directors' => [],
            'administrators' => [],
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => '4' . $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'changeAlertRecipients' => $this->faker->email,
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'departments' => [],
            'instructorGroups' => [],
            'sessionTypes' => [],
            'stewards' => [],
            'vocabularies' => [],
            'directors' => [],
            'administrators' => [],
        );
    }

    public function createInvalid()
    {
        return [
            'id' => 'lkjdsf'
        ];
    }
}
