<?php

namespace Ilios\CoreBundle\Model\LearningMaterials;

use Ilios\CoreBundle\Model\LearningMaterial;
use Ilios\CoreBundle\Model\FileInterface as ResourceInterface;

/**
 * Class File
 * @package Ilios\CoreBundle\Model\LearningMaterials
 */
class File extends LearningMaterial implements FileInterface
{
    /**
     * renamed: relativeFileSystemLocation
     * @var ResourceInterface
     */
    protected $resource;

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
     * @param ResourceInterface $resource
     */
    public function setResource(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
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
}
