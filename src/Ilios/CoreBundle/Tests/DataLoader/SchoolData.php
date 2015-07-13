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
            'deleted' => false,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [1],
            'competencies' => ['1', '2', '3'],
            'courses' => [],
            'programs' => ['1', '2'],
            'departments' => ['1', '2'],
            'disciplines' => ['1', '2'],
            'instructorGroups' => [],
            'curriculumInventoryInstitution' => "1",
            'sessionTypes' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'deleted' => false,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [],
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'departments' => [],
            'disciplines' => [],
            'instructorGroups' => [],
            'sessionTypes' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->word,
            'iliosAdministratorEmail' => $this->faker->email,
            'deleted' => false,
            'changeAlertRecipients' => $this->faker->email,
            'alerts' => [],
            'competencies' => [],
            'courses' => [],
            'programs' => [1],
            'departments' => [],
            'disciplines' => [],
            'instructorGroups' => [],
            'sessionTypes' => []
        );
    }

    public function createInvalid()
    {
        return [
            'id' => 'lkjdsf'
        ];
    }
}
