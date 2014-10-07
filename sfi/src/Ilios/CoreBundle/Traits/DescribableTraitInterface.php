<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface DescribableTraitInterface
 * @package Ilios\CoreBundle\Traits
 */
interface DescribableTraitInterface
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
