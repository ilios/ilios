<?php

namespace Ilios\CoreBundle\Model;

use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\IdentifiableTrait;

/**
 * Class File
 * @package Ilios\CoreBundle\Model
 */
class File implements FileInterface
{
    use IdentifiableTrait;
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @var string The file resource.
     */
    protected $resource;

    /**
     * @param string $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return ($this->getResource() === null) ? null : $this->getUploadRootDir() . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return ($this->getResource() === null) ? null : $this->getUploadDir() . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    private function getUploadRootDir()
    {
        return __DIR__ . "/../../../../web" . $this->getUploadDir();
    }

    /**
     * @todo: Create magic file bucket sauce.
     * @return string
     */
    private function getUploadDir()
    {
        $uploadDir = 'uploads';
        return $uploadDir;
    }
}
