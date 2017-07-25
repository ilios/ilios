<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Permission
 *
 * @ORM\Table(name="permission",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_table_k", columns={"user_id", "table_name", "table_row_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\PermissionRepository")
 *
 * @IS\Entity
 */
class Permission implements PermissionInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="permission_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var UserInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="permissions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_read", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $canRead;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_write", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $canWrite;

    /**
     * @var int
     *
     * @ORM\Column(name="table_row_id", type="integer", nullable=true)
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
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
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $tableName;

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
