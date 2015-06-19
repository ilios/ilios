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
            'updatedAt' => $this->faker->iso8601,
            'sessionType' => "122",
            'course' => "595",
            'disciplines' => ['16'],
            'objectives' => [],
            'meshDescriptors' => [],
            'publishEvent' => "58417",
            'learningMaterials' => [],
            'instructionHours' => [],
            'offerings' => []
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
