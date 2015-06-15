<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ProgramData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => "Doctor of Medicine",
            'shortTitle' => "MD",
            'duration' => 4,
            'deleted' => false,
            'publishedAsTbd' => false,
            'publishEvent' => "15302",
            'owningSchool' => "1",
            'programYears' => ['42','67'],
            'curriculumInventoryReports' => ['9','10']
        );

        $arr[] = array(
            'id' => 7,
            'title' => "Doctor of Pharmacy",
            'shortTitle' => "PharmD",
            'duration' => 4,
            'deleted' => false,
            'publishedAsTbd' => false,
            'publishEvent' => "13",
            'owningSchool' => "3",
            'programYears' => ['58','69'],
            'curriculumInventoryReports' => []
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
