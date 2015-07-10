<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Report
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="report")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Report implements ReportInterface
{
    use TitledEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="report_id", type="integer")
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
     * @ORM\Column(type="string", length=240, nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 240
     * )
    */
    protected $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     *
     * @Assert\NotBlank()
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=32)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="prepositional_object", type="string", length=32, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     */
    protected $prepositionalObject;

    /**
     * @var string
     *
     * @ORM\Column(name="prepositional_object_table_row_id", type="string", length=14, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 14
     * )
     */
    protected $prepositionalObjectTableRowId;

    /**
     * @var boolean
     *
     *@ORM\Column(name="deleted", type="boolean")
     *
     *@Assert\Type(type="bool")
     */
    protected $deleted;

    /**
     * @var UserInterface $user
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="cascade")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $user;

    /**
     * Default createdAt to now
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->deleted = false;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $prepositionalObject
     */
    public function setPrepositionalObject($prepositionalObject)
    {
        $this->prepositionalObject = $prepositionalObject;
    }

    /**
     * @return string
     */
    public function getPrepositionalObject()
    {
        return $this->prepositionalObject;
    }

    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId)
    {
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }

    /**
     * @return string
     */
    public function getPrepositionalObjectTableRowId()
    {
        return $this->prepositionalObjectTableRowId;
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
}
