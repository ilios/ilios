<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 16468,
            'title' => "Case 1",
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => false,
            'deleted' => false,
            'publishedAsTbd' => false,
            'updatedAt' => "2015-06-14T11:18:11+00:00",
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
