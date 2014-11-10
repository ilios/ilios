<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Department
 *
 * @ORM\Entity
 * @ORM\Table(name="department")
 */
class Department implements DepartmentInterface
{
//    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, name="department_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $departmentId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="departments")
     */
    protected $school;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->departmentId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->departmentId : $this->id;
    }

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
