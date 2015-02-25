<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Permission
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="permission",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_table_k", columns={"user_id", "table_name", "table_row_id"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Permission implements PermissionInterface
{
    /**
     * @deprecated Replace with trait in 3.x
     * @var int
     *
     * @ORM\Column(name="permission_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_read", type="boolean")
     * 
     * @Assert\NotBlank()
     * @Assert\Type(type="bool")
     */
    protected $canRead;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_write", type="boolean")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="bool")     
     */
    protected $canWrite;

    /**
     * @var int
     *
     * @ORM\Column(name="table_row_id", type="integer", nullable=true)
     *
     * @Assert\Type(type="integer")
     */
    protected $tableRowId;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", length=30)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")     
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )     
     */
    protected $tableName;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->permissionId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->permissionId : $this->id;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param boolean $canRead
     */
    public function setCanRead($canRead)
    {
        $this->canRead = $canRead;
    }

    /**
     * @return boolean
     */
    public function hasCanRead()
    {
        return $this->canRead;
    }

    /**
     * @param boolean $canWrite
     */
    public function setCanWrite($canWrite)
    {
        $this->canWrite = $canWrite;
    }

    /**
     * @return boolean
     */
    public function hasCanWrite()
    {
        return $this->canWrite;
    }

    /**
     * @param int $tableRowId
     */
    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;
    }

    /**
     * @return int
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
