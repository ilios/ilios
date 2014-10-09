<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;

/**
 * Interface AssessmentOptionInterface
 */
interface AssessmentOptionInterface  extends IdentifiableTraitIntertface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();
}

