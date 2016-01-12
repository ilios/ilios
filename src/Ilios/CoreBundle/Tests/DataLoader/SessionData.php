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
            'publishedAsTbd' => false,
            'published' => true,
            'sessionType' => '1',
            'course' => '1',
            'sessionDescription' => '1',
            'topics' => ['2'],
            'objectives' => ['3'],
            'meshDescriptors' => [],
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
            'topics' => ['1'],
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
            'course' => '2',
            'topics' => ['3'],
            'objectives' => [],
            'meshDescriptors' => ["abc2"],
            'learningMaterials' => ['2'],
            'offerings' => ['6', '7']
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
            'topics' => [],
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
                'course' => '2',
                'ilmSession' => $i - 4,
                'topics' => [],
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
            'topics' => ['1', '2'],
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
