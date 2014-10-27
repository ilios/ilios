<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ilios\CoreBundle\Model\LearningMaterial;

/**
 * Class File
 * @package Ilios\CoreBundle\Model\LearningMaterials
 */
class File extends LearningMaterial implements FileInterface
{
    /*
     * dropped from model
     * - filename
     * - filesize
     * - mime_type
     */
    /**
     * renamed: relative_file_system_location
     * @var string
     */
    protected $path;

    /**
     * renamed copyrightownership
     * @var boolean
     */
    protected $copyrightPermission;

    /**
     * @var string
     */
    protected $copyrightRationale;

    /**
     * @var UploadedFile;
     */
    protected $resource;

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission)
    {
        $this->copyrightPermission = $copyrightPermission;
    }

    /**
     * @return bool
     */
    public function hasCopyrightPermission()
    {
        return $this->copyrightPermission;
    }

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale)
    {
        $this->copyrightRationale = $copyrightRationale;
    }

    /**
     * @return string
     */
    public function getCopyrightRationale()
    {
        return $this->copyrightRationale;
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
     * @param UploadedFile $resource
     */
    public function setResource(UploadedFile $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return UploadedFile
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return void
     */
    public function upload()
    {
        if ($this->getResource() === null) {
            return;
        }

        $this->getResource()->move(
            $this->getUploadRootDir()
        );
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
