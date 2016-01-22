<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventorySequenceBlockSessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'sequenceBlock' => '1',
            'session' => '1',
            'countOfferingsOnce' => false
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 2,
            'sequenceBlock' => '1',
            'session' => '2',
            'countOfferingsOnce' => true
        );
    }

    public function createInvalid()
    {
        return [
            'sequenceBlock' => '4'
        ];
    }
}
