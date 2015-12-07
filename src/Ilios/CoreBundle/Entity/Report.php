<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\SchoolEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Report
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="report")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Report implements ReportInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledEntity;
    use SchoolEntity;

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
     *
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("createdAt")
     */
    protected $createdAt;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $school;

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
     *
     * @JMS\Expose
     * @JMS\Type("string")
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
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("prepositionalObject")
     */
    protected $prepositionalObject;

    /**
     * @var string
     *
     * @ORM\Column(name="prepositional_object_table_row_id", type="string", length=14, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 14
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("prepositionalObjectTableRowId")
     */
    protected $prepositionalObjectTableRowId;

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
        $this->createdAt = new \DateTime();
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
