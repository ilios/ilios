<?php

namespace Tests\CoreBundle\DataLoader;

/**
 * Class PermissionData
 */
class PermissionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'user' => '2',
            'canRead' => true,
            'canWrite' => true,
            'tableRowId' => 2,
            'tableName' => 'school',
        ];
        $arr[] = [
            'id' => 2,
            'user' => '2',
            'canRead' => true,
            'canWrite' => false,
            'tableRowId' => 1,
            'tableName' => 'course',
        ];
        $arr[] = [
            'id' => 3,
            'user' => '2',
            'canRead' => false,
            'canWrite' => true,
            'tableRowId' => 1,
            'tableName' => 'program',
        ];

        $arr[] = [
            'id' => 4,
            'user' => '2',
            'canRead' => true,
            'canWrite' => true,
            'tableRowId' => 3,
            'tableName' => 'school',
        ];
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 5,
            'user' => '1',
            'canRead' => true,
            'canWrite' => false,
            'tableRowId' => 1,
            'tableName' => 'program',
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
