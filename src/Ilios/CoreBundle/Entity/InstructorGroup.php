<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\LearnerGroupsEntity;
use Ilios\CoreBundle\Traits\UsersEntity;
use JMS\Serializer\Annotation as JMS;
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
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="instructor_group")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\InstructorGroupRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
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

    /**
     * @var int
     *
     * @ORM\Column(name="instructor_group_id", type="integer")
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
     * @JMS\Expose
     * @JMS\Type("string")
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
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $school;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="instructorGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    protected $learnerGroups;

    /**
     * @var ArrayCollection|IlmSession[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="instructorGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("ilmSessions")
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
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $users;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="instructorGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     * @param Collection $ilmSessions
     */
    public function setIlmSessions(Collection $ilmSessions)
    {
        $this->ilmSessions = new ArrayCollection();

        foreach ($ilmSessions as $ilmSession) {
            $this->addIlmSession($ilmSession);
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
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getIlmSessions()
    {
        return $this->ilmSessions;
    }
}
