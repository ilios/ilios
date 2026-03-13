<?php

namespace App\Monitor;

use Laminas\Diagnostics\Check\CheckInterface;

class DatabaseConnection implements CheckInterface
{

    /**
     * @inheritDoc
     */
    public function check()
    {
        // TODO: Implement check() method.
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        // TODO: Implement getLabel() method.
    }
}
