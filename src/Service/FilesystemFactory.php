<?php

namespace App\Service;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as LeagueFilesystem;

class FilesystemFactory
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function getFilesystem() : LeagueFilesystem
    {
        $path = $this->config->get('file_system_storage_path');
        $adapter = new Local($path);
        return new LeagueFilesystem($adapter, ['visibility' => 'private']);
    }
}
