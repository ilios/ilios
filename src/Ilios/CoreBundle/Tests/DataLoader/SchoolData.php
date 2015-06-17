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
            'programs' => [1],
            'departments' => [],
            'disciplines' => [],
            'instructorGroups' => [],
            'curriculumInventoryInsitution' => "1",
            'sessionTypes' => ['1', '2']
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
