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
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'user' => 2,
            'canRead' => true,
            'canWrite' => true,
            'tableRowId' => 2,
            'tableName' => 'school',
        );

        return $arr;
    }

    public function create()
    {
        throw new \Exception('not implemented.');
    }

    public function createInvalid()
    {
        return [];
    }
}
