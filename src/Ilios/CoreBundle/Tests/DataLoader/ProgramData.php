<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ProgramData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->title(15),
            'shortTitle' => $this->faker->title(5),
            'duration' => 4,
            'deleted' => false,
            'publishedAsTbd' => false,
            'publishEvent' => '1',
            'owningSchool' => "1",
            'programYears' => ["1", "2"],
            'curriculumInventoryReports' => []
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
