<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class AuditLog
 *
 * @ORM\Table(name="audit_log")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AuditLogRepository")
 *
 */
class AuditLog implements AuditLogInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16
     * )
     *
     */
    protected $action;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     *
     */
    protected $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     */
    protected $objectId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     *
     */
    protected $objectClass;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=1000)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 1000
     * )
     *
     */
    protected $valuesChanged;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="auditLogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Set action
     *
     * @param string $action
     *
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     *
     */
    public function setObjectId($objectId)
    {
        $this->objectId = (int) $objectId;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectClass
     *
     * @param string $objectClass
     *
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;
    }

    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Set valuesChanged
     *
     * @param string $valuesChanged
     *
     */
    public function setValuesChanged($valuesChanged)
    {
        $this->valuesChanged = $valuesChanged;
    }

    /**
     * Get valuesChanged
     *
     * @return string
     */
    public function getValuesChanged()
    {
        return $this->valuesChanged;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user = null)
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
}
