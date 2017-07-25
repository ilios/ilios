<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\IlmSessionsEntity;
use Ilios\CoreBundle\Traits\LearnerGroupsEntity;
use Ilios\CoreBundle\Traits\UsersEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\OfferingsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class InstructorGroup
 *
 * @ORM\Table(name="instructor_group")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\InstructorGroupRepository")
 *
 * @IS\Entity
 */
class InstructorGroup implements InstructorGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use SchoolEntity;
    use LearnerGroupsEntity;
    use UsersEntity;
    use IlmSessionsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="instructor_group_id", type="integer")
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
     * @ORM\Column(type="string", length=60)
     * @var string
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
     * @var SchoolInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="instructorGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="instructorGroups")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $learnerGroups;

    /**
     * @var ArrayCollection|IlmSession[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="instructorGroups")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $ilmSessions;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="instructorGroups")
     * @ORM\JoinTable(name="instructor_group_x_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id")
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
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="instructorGroups")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $offerings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->ilmSessions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->offerings = new ArrayCollection();
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
            $learnerGroup->addInstructorGroup($this);
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if ($this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->removeElement($learnerGroup);
            $learnerGroup->removeInstructorGroup($this);
        }
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession)
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
            $ilmSession->addInstructorGroup($this);
        }
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function removeIlmSession(IlmSessionInterface $ilmSession)
    {
        if ($this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->removeElement($ilmSession);
            $ilmSession->removeInstructorGroup($this);
        }
    }
}
