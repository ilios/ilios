<?php

namespace Tests\CoreBundle\DataLoader;

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
            'publishedAsTbd' => false,
            'published' => true,
            'sessionType' => '1',
            'course' => '1',
            'sessionDescription' => '1',
            'terms' => ['2', '5'],
            'objectives' => ['2', '3'],
            'meshDescriptors' => ['abc1'],
            'learningMaterials' => ['1'],
            'offerings' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'attireRequired' => true,
            'equipmentRequired' => false,
            'supplemental' => true,
            'publishedAsTbd' => false,
            'published' => false,
            'sessionType' => '2',
            'course' => '1',
            'sessionDescription' => '2',
            'terms' => ['1', '4'],
            'objectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => ['3', '4', '5']
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => true,
            'publishedAsTbd' => false,
            'published' => false,
            'sessionType' => '2',
            'course' => '2',
            'terms' => ['3', '6'],
            'objectives' => [],
            'meshDescriptors' => ["abc2"],
            'learningMaterials' => ['2'],
            'offerings' => ['6', '7', '8']
        );

        $arr[] = array(
            'id' => 4,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'sessionType' => '2',
            'course' => '4',
            'terms' => [],
            'objectives' => ['6', '7'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => []
        );
        
        for ($i = 5; $i <= 8; $i++) {
            $arr[] = array(
                'id' => $i,
                'title' => $this->faker->text(10),
                'attireRequired' => false,
                'equipmentRequired' => false,
                'supplemental' => false,
                'publishedAsTbd' => false,
                'published' => false,
                'sessionType' => '1',
                'course' => '2',
                'ilmSession' => $i - 4,
                'terms' => [],
                'objectives' => [],
                'meshDescriptors' => [],
                'learningMaterials' => [],
                'offerings' => []
            );
        }

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 9,
            'title' => $this->faker->text(10),
            'attireRequired' => true,
            'equipmentRequired' => true,
            'supplemental' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'sessionType' => '1',
            'course' => '1',
            'terms' => ['1', '2'],
            'objectives' => ['3'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => []
        );
    }

    public function createInvalid()
    {
        return [
            'course' => 11
        ];
    }
}
