<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

interface FileInterface extends LearningMaterialInterface
{
    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission);

    /**
     * @return bool
     */
    public function hasCopyrightPermission();

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale);

    /**
     * @return string
     */
    public function getCopyrightRationale();


    /**
     * @return string
     */
    public function getAbsolutePath();

    /**
     * @return string
     */
    public function getWebPath();

    /**
     * @param UploadedFile $resource
     */
    public function setResource(UploadedFile $resource);

    /**
     * @return UploadedFile|\SplFileInfo
     */
    public function getResource();

    /**
     * @return void
     */
    public function upload();
}
