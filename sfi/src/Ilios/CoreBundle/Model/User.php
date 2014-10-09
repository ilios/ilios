<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Model\ApiKey;
use Ilios\CoreBundle\Model\School;
use Ilios\CoreBundle\Model\UserMadeReminder;
use Ilios\CoreBundle\Model\Course;
use Ilios\CoreBundle\Model\Group;
use Ilios\CoreBundle\Model\InstructorGroup;
use Ilios\CoreBundle\Model\Offering;
use Ilios\CoreBundle\Model\ProgramYear;
use Ilios\CoreBundle\Model\Alert;
use Ilios\CoreBundle\Model\UserRole;
use Ilios\CoreBundle\Model\LearningMaterial;
use Ilios\CoreBundle\Model\PublishEvent;
use Ilios\CoreBundle\Model\Report;

/**
 * User
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var boolean
     */
    protected $addedViaIlios;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $ucUid;

    /**
     * @var string
     */
    protected $otherId;

    /**
     * @var boolean
     */
    protected $examined;

    /**
     * @var boolean
     */
    protected $userSyncIgnore;

    /**
     * @var ApiKey
     */
    protected $apiKey;

    /**
     * @var Collection
     */
    protected $reminders;

    /**
     * @var School
     */
    protected $primarySchool;

    /**
     * @var Collection
     */
    protected $directedCourses;

    /**
     * @var Collection
     */
    protected $userGroups;

    /**
     * @var Collection
     */
    protected $instructorUserGroups;

    /**
     * @var Collection
     */
    protected $instructorGroups;

    /**
     * @var Collection
     */
    protected $offerings;

    /**
     * @var Collection
     */
    protected $programYears;

    /**
     * @var Collection
     */
    protected $alerts;

    /**
     * @var Collection
     */
    protected $roles;

    /**
     * @var Collection
     */
    protected $learningMaterials;

    /**
     * @var Collection
     */
    protected $publishEvents;

    /**
     * @var Collection
     */
    protected $reports;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reminders            = new ArrayCollection();
        $this->directedCourses      = new ArrayCollection();
        $this->userGroups           = new ArrayCollection();
        $this->instructorUserGroups = new ArrayCollection();
        $this->instructorGroups     = new ArrayCollection();
        $this->offerings            = new ArrayCollection();
        $this->programYears         = new ArrayCollection();
        $this->alerts               = new ArrayCollection();
        $this->roles                = new ArrayCollection();
        $this->learningMaterials    = new ArrayCollection();
        $this->publishEvents        = new ArrayCollection();
        $this->reports              = new ArrayCollection();
        $this->addedViaIlios = false;
        $this->enabled = true;
        $this->examined = false;
        $this->userSyncIgnore = false;
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set ID
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set addedViaIlios
     *
     * @param boolean $addedViaIlios
     * @return User
     */
    public function setAddedViaIlios($addedViaIlios)
    {
        $this->addedViaIlios = $addedViaIlios;

        return $this;
    }

    /**
     * Get addedViaIlios
     *
     * @return boolean
     */
    public function getAddedViaIlios()
    {
        return $this->addedViaIlios;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set ucUid
     *
     * @param string $ucUid
     * @return User
     */
    public function setUcUid($ucUid)
    {
        $this->ucUid = $ucUid;

        return $this;
    }

    /**
     * Get ucUid
     *
     * @return string
     */
    public function getUcUid()
    {
        return $this->ucUid;
    }

    /**
     * Set otherId
     *
     * @param string $otherId
     * @return User
     */
    public function setOtherId($otherId)
    {
        $this->otherId = $otherId;

        return $this;
    }

    /**
     * Get otherId
     *
     * @return string
     */
    public function getOtherId()
    {
        return $this->otherId;
    }

    /**
     * Set examined
     *
     * @param boolean $examined
     * @return User
     */
    public function setExamined($examined)
    {
        $this->examined = $examined;

        return $this;
    }

    /**
     * Get examined
     *
     * @return boolean
     */
    public function getExamined()
    {
        return $this->examined;
    }

    /**
     * Set userSyncIgnore
     *
     * @param boolean $userSyncIgnore
     * @return User
     */
    public function setUserSyncIgnore($userSyncIgnore)
    {
        $this->userSyncIgnore = $userSyncIgnore;

        return $this;
    }

    /**
     * Get userSyncIgnore
     *
     * @return boolean
     */
    public function getUserSyncIgnore()
    {
        return $this->userSyncIgnore;
    }

    /**
     * Set apiKey
     *
     * @param ApiKey $apiKey
     * @return User
     */
    public function setApiKey(ApiKey $apiKey = null)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return ApiKey
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Add reminders
     *
     * @param UserMadeReminder $reminders
     * @return User
     */
    public function addReminder(UserMadeReminder $reminders)
    {
        $this->reminders[] = $reminders;

        return $this;
    }

    /**
     * Remove reminders
     *
     * @param UserMadeReminder $reminders
     */
    public function removeReminder(UserMadeReminder $reminders)
    {
        $this->reminders->removeElement($reminders);
    }

    /**
     * Get reminders
     *
     * @return UserMadeReminder[]
     */
    public function getReminders()
    {
        return $this->reminders->toArray();
    }

    /**
     * Set primarySchool
     *
     * @param School $primarySchool
     * @return User
     */
    public function setPrimarySchool(School $primarySchool = null)
    {
        $this->primarySchool = $primarySchool;

        return $this;
    }

    /**
     * Get primarySchool
     *
     * @return School
     */
    public function getPrimarySchool()
    {
        return $this->primarySchool;
    }

    /**
     * Add directedCourses
     *
     * @param Course $course
     * @return User
     */
    public function addDirectedCourse(Course $course)
    {
        $this->directedCourses[] = $course;

        return $this;
    }

    /**
     * Remove directedCourses
     *
     * @param Course $course
     */
    public function removeDirectedCourse(Course $course)
    {
        $this->directedCourses->removeElement($course);
    }

    /**
     * Get directedCourses
     *
     * @return Course[]
     */
    public function getDirectedCourses()
    {
        return $this->directedCourses->toArray();
    }

    /**
     * Add userGroups
     *
     * @param Group $userGroups
     * @return User
     */
    public function addUserGroup(Group $userGroups)
    {
        $this->userGroups[] = $userGroups;

        return $this;
    }

    /**
     * Remove userGroups
     *
     * @param Group $userGroups
     */
    public function removeUserGroup(Group $userGroups)
    {
        $this->userGroups->removeElement($userGroups);
    }

    /**
     * Get userGroups
     *
     * @return Group[]
     */
    public function getUserGroups()
    {
        return $this->userGroups->toArray();
    }

    /**
     * Add instructorUserGroups
     *
     * @param Group $instructorUserGroups
     * @return User
     */
    public function addInstructorUserGroup(Group $instructorUserGroups)
    {
        $this->instructorUserGroups[] = $instructorUserGroups;

        return $this;
    }

    /**
     * Remove instructorUserGroups
     *
     * @param Group $instructorUserGroups
     */
    public function removeInstructorUserGroup(Group $instructorUserGroups)
    {
        $this->instructorUserGroups->removeElement($instructorUserGroups);
    }

    /**
     * Get instructorUserGroups
     *
     * @return Group[]
     */
    public function getInstructorUserGroups()
    {
        return $this->instructorUserGroups->toArray();
    }

    /**
     * Add instructorGroups
     *
     * @param InstructorGroup $instructorGroups
     * @return User
     */
    public function addInstructorGroup(InstructorGroup $instructorGroups)
    {
        $this->instructorGroups[] = $instructorGroups;

        return $this;
    }

    /**
     * Remove instructorGroups
     *
     * @param InstructorGroup $instructorGroups
     */
    public function removeInstructorGroup(InstructorGroup $instructorGroups)
    {
        $this->instructorGroups->removeElement($instructorGroups);
    }

    /**
     * Get instructorGroups
     *
     * @return InstructorGroup[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups->toArray();
    }

    /**
     * Add offerings
     *
     * @param Offering $offerings
     * @return User
     */
    public function addOffering(Offering $offerings)
    {
        $this->offerings[] = $offerings;

        return $this;
    }

    /**
     * Remove offerings
     *
     * @param Offering $offerings
     */
    public function removeOffering(Offering $offerings)
    {
        $this->offerings->removeElement($offerings);
    }

    /**
     * Get offerings
     *
     * @return Offering[]
     */
    public function getOfferings()
    {
        return $this->offerings->toArray();
    }

    /**
     * Add programYears
     *
     * @param ProgramYear $programYears
     * @return User
     */
    public function addProgramYear(ProgramYear $programYears)
    {
        $this->programYears[] = $programYears;

        return $this;
    }

    /**
     * Remove programYears
     *
     * @param ProgramYear $programYears
     */
    public function removeProgramYear(ProgramYear $programYears)
    {
        $this->programYears->removeElement($programYears);
    }

    /**
     * Get programYears
     *
     * @return ProgramYear[]
     */
    public function getProgramYears()
    {
        return $this->programYears->toArray();
    }

    /**
     * Add alerts
     *
     * @param Alert $alerts
     * @return User
     */
    public function addAlert(Alert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param Alert $alerts
     */
    public function removeAlert(Alert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return Alert[]
     */
    public function getAlerts()
    {
        return $this->alerts->toArray();
    }

    /**
     * Add roles
     *
     * @param UserRole $roles
     * @return User
     */
    public function addRole(UserRole $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param UserRole $roles
     */
    public function removeRole(UserRole $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return UserRole[]
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Add learningMaterial
     *
     * @param LearningMaterial $learningMaterial
     * @return User
     */
    public function addLearningMaterial(LearningMaterial $learningMaterial)
    {
        $this->learningMaterials[] = $learningMaterial;

        return $this;
    }

    /**
     * Remove learningMaterial
     *
     * @param LearningMaterial $learningMaterial
     */
    public function removeLearningMaterial(LearningMaterial $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    /**
     * Get learningMaterials
     *
     * @return LearningMaterial[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials->toArray();
    }

    /**
     * Add publishEvent
     *
     * @param PublishEvent $publishEvent
     * @return User
     */
    public function addPublishEvent(PublishEvent $publishEvent)
    {
        $this->publishEvents[] = $publishEvent;

        return $this;
    }

    /**
     * Remove publishEvent
     *
     * @param PublishEvent $publishEvent
     */
    public function removePublishEvent(PublishEvent $publishEvent)
    {
        $this->publishEvents->removeElement($publishEvent);
    }

    /**
     * Get publishEvents
     *
     * @return PublishEvent[]
     */
    public function getPublishEvents()
    {
        return $this->publishEvents->toArray();
    }

    /**
     * Add report
     *
     * @param Report $report
     * @return User
     */
    public function addReport(Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param Report $report
     */
    public function removeReport(Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get reports
     *
     * @return Report[]
     */
    public function getReports()
    {
        return $this->reports->toArray();
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(array(
                $this->userId,
                $this->ucUid,
                $this->email
            ));

    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list (
            $this->userId,
            $this->ucUid,
            $this->email
            ) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return (string) $this->userId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }
}
