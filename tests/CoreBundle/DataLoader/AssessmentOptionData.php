<?php

namespace Tests\CoreBundle\DataLoader;

class AssessmentOptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'name' => $this->faker->word,
            'sessionTypes' => [1]
        );

        $arr[] = array(
            'id' => 2,
            'name' => 'second option',
            'sessionTypes' => [2]
        );
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'name' => $this->faker->text(10),
            'sessionTypes' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'something',
            'name' => $this->faker->text
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
            $arr['id'] = $arr['id'] + $i;
            $arr['name'] = $arr['id'] . $this->faker->word;
            $data[] = $arr;
        }

        return $data;
    }
}
