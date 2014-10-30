<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface DescribableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface DescribableEntityInterface
{
    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();
}
