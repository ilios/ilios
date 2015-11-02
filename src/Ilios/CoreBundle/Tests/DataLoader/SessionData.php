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
            'sessionDescription' => '1',
            'topics' => ['1', '2'],
            'objectives' => ['1', '2'],
            'meshDescriptors' => [],
            'publishEvent' => '4',
            'learningMaterials' => ['1'],
            'offerings' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => false,
            'deleted' => true,
            'publishedAsTbd' => false,
            'sessionType' => '1',
            'course' => '1',
            'sessionDescription' => '2',
            'topics' => [],
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
            'supplemental' => false,
            'deleted' => false,
            'publishedAsTbd' => false,
            'course' => '2',
            'topics' => [],
            'objectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => ['6', '7']
        );
        
        for ($i = 4; $i <= 7; $i++) {
            $arr[] = array(
                'id' => $i,
                'title' => $this->faker->text(10),
                'attireRequired' => false,
                'equipmentRequired' => false,
                'supplemental' => false,
                'deleted' => false,
                'publishedAsTbd' => false,
                'course' => '2',
                'ilmSession' => $i - 3,
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
            'id' => 8,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => false,
            'deleted' => false,
            'publishedAsTbd' => false,
            'sessionType' => '1',
            'course' => '1',
            'topics' => ['1', '2'],
            'objectives' => ['1', '2'],
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
    
    public function removeDeletedSessionsFromArray(array $data)
    {
        $deletedSessions = array_filter($this->getAll(), function ($session) {
            return $session['deleted'];
        });
        
        $deletedSessionIds =  array_map(function ($session) {
            return $session['id'];
        }, $deletedSessions);

        return array_map(function ($arr) use ($deletedSessionIds) {
            $arr['sessions'] = array_filter($arr['sessions'], function ($id) use ($deletedSessionIds) {
                return !in_array($id, $deletedSessionIds);
            });
            
            return $arr;
        }, $data);
    }
}
