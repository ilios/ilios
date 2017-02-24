<?php

namespace Tests\CoreBundle\DataLoader;

class AamcPcrsData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 'aamc-pcrs-comp-c0101',
            'description' => $this->faker->text,
            'competencies' => [1,2]
        );
        $arr[] = array(
            'id' => 'aamc-pcrs-comp-c0102',
            'description' => 'second description',
            'competencies' => [2,3]
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 'fk-',
            'description' => $this->faker->text,
            'competencies' => [1]
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => $this->faker->text,
            'competencies' => [454098430958]
        ];
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
