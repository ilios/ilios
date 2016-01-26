<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class VocabularyData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'title' => 'B' . $this->faker->text(99),
            'hierarchical' => false,
            'school' => '1',
            'terms' => ['1', '2', '3']
        );
        $arr[] = array(
            'id' => 2,
            'title' => 'A' . $this->faker->text(99),
            'hierarchical' => true,
            'school' => '2',
            'terms' => ['4', '5']
        );
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'title' => 'C' . $this->faker->text(99),
            'hierarchical' => true,
            'school' => '2',
            'terms' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'school' => 555,
        ];
    }
}
