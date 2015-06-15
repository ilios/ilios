<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

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
            'name' => $this->faker->word,
            'sessionTypes' => [2]
        );
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'name' => $this->faker->text(10),
            'sessionTypes' => [2]
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'something',
            'name' => $this->faker->text,
            'sessionTypes' => [10000]
        ];
    }
}
