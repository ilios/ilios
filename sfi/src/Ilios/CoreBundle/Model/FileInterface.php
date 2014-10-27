<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\BlameableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\TimestampableTraitinterface;

/**
 * Interface FileInterface
 * @package Ilios\CoreBundle\Model
 */
interface FileInterface extends
    IdentifiableTraitInterface,
    TimestampableTraitinterface,
    BlameableTraitInterface
{
    /**
     * @param string $resource
     */
    public function setResource($resource);

    /**
     * @return string
     */
    public function getResource();

    /**
     * @return string
     */
    public function getAbsolutePath();

    /**
     * @return string
     */
    public function getWebPath();
}
