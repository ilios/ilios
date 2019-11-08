<?php

namespace App\Service;

class NonCachingIliosFileSystem extends IliosFileSystem
{
    public function __construct(FilesystemFactory $factory)
    {
        $fileSystem = $factory->getNonCachingFilesystem();
        parent::__construct($fileSystem);
    }
}
