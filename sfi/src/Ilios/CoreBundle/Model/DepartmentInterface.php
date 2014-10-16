<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface DepartmentInterface
 */
interface DepartmentInterface 
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

