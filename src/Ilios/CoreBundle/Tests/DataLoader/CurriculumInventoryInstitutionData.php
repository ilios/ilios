<?php

namespace IliosCoreBundleTestsDataLoader;

class CurriculumInventoryInstitutionData extends AbstractDataLoader
{
    protected function getData()
    {

        $arr = array();

        $arr[undefined] = array(
            'name' => "University of California, San Francisco, School Of Medicine",
            'aamcCode' => "108",
            'addressStreet' => "513 Parnassus Ave",
            'address_city' => "San Francisco",
            'address_state_or_province' => "CA",
            'addressZipCode' => "94143",
            'addressCountryCode' => "US",
            'school' => "1"
        );

        $arr[undefined] = array(
            'name' => "Pharmacy",
            'aamcCode' => "00000",
            'addressStreet' => "",
            'address_city' => "",
            'address_state_or_province' => "",
            'addressZipCode' => "",
            'addressCountryCode' => "",
            'school' => "3"
        );

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
