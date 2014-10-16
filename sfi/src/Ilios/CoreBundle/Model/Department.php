<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\TitleTrait;


/**
 * Department
 */
class Department implements DepartmentInterface
{
    use IdentifiableTrait;
    use TitleTrait;

    /**
     * @var SchoolInterface
     */
    protected $school;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }

    /**
     * @return SchoolInterface 
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean 
     */
    public function isDeleted()
    {
        return $this->deleted;
    }
}
