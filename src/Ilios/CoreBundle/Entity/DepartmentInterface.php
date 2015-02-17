<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface DepartmentInterface
 */
interface DepartmentInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();
}
