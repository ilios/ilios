<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 26390,
            'title' => "'Internal Organizaiton of the Cell'",
            'description' => "An advanced text that correlates cell structure with function at a<br>molecular level",
            'uploadDate' => "2014-08-22T17:22:39+00:00",
            'originalAuthor' => "Morgan, Kathleen",
            'token' => "9090d3eee33078ceb70c6800bf67d2a0c5555f229c8b656a70b3ba2df9fca0b5",
            'userRole' => "1",
            'status' => "1",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'citation' => $this->faker->text,
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 28414,
            'title' => "Somite Clock Video (MP4)",
            'description' => "Additional video showing the 'somite clock'",
            'uploadDate' => "2014-10-15T18:32:52+00:00",
            'originalAuthor' => "Murray, Larry",
            'token' => "ab82cf85803c12902b16591bbd8a9d55d653b09c950707bb53a8c7cc254398d8",
            'userRole' => "3",
            'status' => "2",
            'owningUser' => "4136",
            'courseLearningMaterials' => [],
            'path' => "/learning_materials/595/16556/20141015-113252_625_2b4a3343c90ff70da415fd138de5d96d",
            'copyrightPermission' => true,
            'copyrightRationale' => "",
            'filename' => "ilios_demofile.pdf",
            'mimetype' => "application/pdf",
            'filesize' => 2318,
            'type' => "file"
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
