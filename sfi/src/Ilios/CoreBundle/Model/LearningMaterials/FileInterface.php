<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterialInterface;

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

}
