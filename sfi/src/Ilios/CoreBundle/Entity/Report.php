<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

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
     * @deprecated To be replaced by Identifiable Trait in 3.x
     * @var int
     *
     * @ORM\Column(name="report_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @ORM\Column(type="string", length=240, nullable=true)
    * @todo should be on the TitledEntity Trait
    * @var string
    */
    protected $title;

    /**
     * @deprecated To be replaced by Timestampable trait in 3.x
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    protected $creationDate;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=32)
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="prepositional_object", type="string", length=32, nullable=true)
     */
    protected $prepositionalObject;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
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
    * @var ReportPoValueInterface
    *
    * @ORM\OneToOne(targetEntity="ReportPoValue", mappedBy="report")
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $poValue;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->reportId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->reportId : $this->id;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->creationDate = $createdAt;
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return ($this->createdAt === null) ? $this->creationDate : $this->createdAt;
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
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
