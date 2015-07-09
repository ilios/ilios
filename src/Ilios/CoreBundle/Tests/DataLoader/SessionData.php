<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => false,
            'deleted' => false,
            'publishedAsTbd' => false,
            'sessionType' => '1',
            'course' => '1',
            'ilmSessionFacet' => '1',
            'disciplines' => ['1', '2'],
            'objectives' => ['1', '2'],
            'meshDescriptors' => [],
            'publishEvent' => '1',
            'sessionLearningMaterials' => ['1'],
            'instructionHours' => [],
            'offerings' => ['1']
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => false,
            'deleted' => false,
            'publishedAsTbd' => false,
            'sessionType' => '1',
            'course' => '1',
            'disciplines' => ['1', '2'],
            'objectives' => ['1', '2'],
            'meshDescriptors' => [],
            'publishEvent' => '1',
            'sessionLearningMaterials' => ["1"],
            'instructionHours' => [],
            'offerings' => ['1']
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
