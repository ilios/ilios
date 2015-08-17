<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface DepartmentInterface
 */
interface DepartmentInterface extends IdentifiableEntityInterface, TitledEntityInterface, SchoolEntityInterface
{
    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();
}
