<?php

namespace Tests\CoreBundle\DataLoader;

/**
 * Class AamcResourceTypeData
 */
class AamcResourceTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 'RE001',
            'title' => 'first title',
            'description' => $this->faker->text,
            'terms' => ['1','2'],
        );

        $arr[] = array(
            'id' => 'RE002',
            'title' => $this->faker->text(100),
            'description' => 'second description',
            'terms' => ['2', '3'],
        );

        $arr[] = array(
            'id' => 'RE003',
            'title' =>$this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => [],
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 'FKRE',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function createMany($count)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] . $i;
            $data[] = $arr;
        }

        return $data;
    }
}
