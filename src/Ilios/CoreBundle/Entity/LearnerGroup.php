<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\IlmSessionsEntity;
use Ilios\CoreBundle\Traits\InstructorGroupsEntity;
use Ilios\CoreBundle\Traits\InstructorsEntity;
use Ilios\CoreBundle\Traits\UsersEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\OfferingsEntity;

/**
 * Class LearnerGroup
 *
 * @ORM\Table(name="`group`")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\LearnerGroupRepository")
 *
 * @IS\Entity
 */
class LearnerGroup implements LearnerGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use UsersEntity;
    use InstructorsEntity;
    use InstructorGroupsEntity;
    use IlmSessionsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer")
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
     * @var string
     *
     * @ORM\Column(type="string", length=60)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=100, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $location;

    /**
     * @var CohortInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Cohort", inversedBy="learnerGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cohort_id", referencedColumnName="cohort_id", onDelete="CASCADE", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $cohort;

    /**
     * @var LearnerGroupInterface
     *
     * @ORM\ManyToOne(targetEntity="LearnerGroup", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_group_id", referencedColumnName="group_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $parent;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearnerGroup", mappedBy="parent")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $children;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="learnerGroups")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $ilmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="learnerGroups")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $offerings;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="InstructorGroup", inversedBy="learnerGroups")
     * @ORM\JoinTable(name="group_x_instructor_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="learnerGroups")
     * @ORM\JoinTable(name="group_x_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $users;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="instructedLearnerGroups")
     * @ORM\JoinTable(name="group_x_instructor",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $instructors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users            = new ArrayCollection();
        $this->ilmSessions      = new ArrayCollection();
        $this->offerings        = new ArrayCollection();
        $this->children         = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->instructors      = new ArrayCollection();
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return CohortInterface
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession)
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
            $ilmSession->addLearnerGroup($this);
        }
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function removeIlmSession(IlmSessionInterface $ilmSession)
    {
        if ($this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->removeElement($ilmSession);
            $ilmSession->removeLearnerGroup($this);
        }
    }

    /**
     * @param LearnerGroupInterface $parent
     */
    public function setParent(LearnerGroupInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return LearnerGroupInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children = null)
    {
        $this->children = new ArrayCollection();
        if (is_null($children)) {
            return;
        }

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param LearnerGroupInterface $child
     */
    public function addChild(LearnerGroupInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    /**
     * @param LearnerGroupInterface $child
     */
    public function removeChild(LearnerGroupInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function addOffering(OfferingInterface $offering)
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->addLearnerGroup($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeOffering(OfferingInterface $offering)
    {
        if ($this->offerings->contains($offering)) {
            $this->offerings->removeElement($offering);
            $offering->removeLearnerGroup($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($cohort = $this->getCohort()) {
            return $cohort->getSchool();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getProgram()
    {
        if ($cohort = $this->getCohort()) {
            return $cohort = $cohort->getProgram();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getProgramYear()
    {
        if ($cohort = $this->getCohort()) {
            return $cohort->getProgramYear();
        }
        return null;
    }
}
