<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterialInterface;
use Ilios\CoreBundle\Model\FileInterface as ResourceInterface;

interface FileInterface extends LearningMaterialInterface
{
    /**
     * @param ResourceInterface $resource
     */
    public function setResource(ResourceInterface $resource);

    /**
     * @return ResourceInterface
     */
    public function getResource();

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
}
