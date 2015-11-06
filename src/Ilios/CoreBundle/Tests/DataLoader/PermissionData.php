<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

/**
 * Class PermissionData
 * @package Ilios\CoreBundle\Tests\DataLoader
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
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
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
