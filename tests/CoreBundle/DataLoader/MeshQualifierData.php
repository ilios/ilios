<?php

namespace Tests\CoreBundle\DataLoader;

class MeshQualifierData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => '1',
            'name' => 'qual' . $this->faker->text(5),
            'descriptors' => ['abc1']
        );
        $arr[] = array(
            'id' => '2',
            'name' => 'second qualifier',
            'descriptors' => ['abc1']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => '3',
            'name' => $this->faker->text(5),
            'descriptors' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }

    /**
     * Mesh qualifier IDs are strings so we have to convert them
     * @inheritdoc
     */
    public function createMany($count)
    {
        $data = parent::createMany($count);

        return array_map(function (array $arr) {
            $arr['id'] = (string) $arr['id'];

            return $arr;
        }, $data);
    }
}
