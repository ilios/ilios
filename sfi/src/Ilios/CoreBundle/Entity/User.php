<?php

namespace Ilios\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $middleName;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var boolean
     */
    private $addedViaIlios;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var string
     */
    private $ucUid;

    /**
     * @var string
     */
    private $otherId;

    /**
     * @var boolean
     */
    private $examined;

    /**
     * @var boolean
     */
    private $userSyncIgnore;

    /**
     * @var \Ilios\CoreBundle\Entity\ApiKey
     */
    private $apiKey;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reminders;

    /**
     * @var \Ilios\CoreBundle\Entity\School
     */
    private $primarySchool;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $directedCourses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructorUserGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructorGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $offerings;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $programYears;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $alerts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $learningMaterials;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $publishEvents;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reports;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reminders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->directedCourses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructorUserGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructorGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offerings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->programYears = new \Doctrine\Common\Collections\ArrayCollection();
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->learningMaterials = new \Doctrine\Common\Collections\ArrayCollection();
        $this->publishEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->addedViaIlios = false;
        $this->enabled = true;
        $this->examined = false;
        $this->userSyncIgnore = false;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
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
     * @param \Ilios\CoreBundle\Entity\ApiKey $apiKey
     * @return User
     */
    public function setApiKey(\Ilios\CoreBundle\Entity\ApiKey $apiKey = null)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return \Ilios\CoreBundle\Entity\ApiKey
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Add reminders
     *
     * @param \Ilios\CoreBundle\Entity\UserMadeReminder $reminders
     * @return User
     */
    public function addReminder(\Ilios\CoreBundle\Entity\UserMadeReminder $reminders)
    {
        $this->reminders[] = $reminders;

        return $this;
    }

    /**
     * Remove reminders
     *
     * @param \Ilios\CoreBundle\Entity\UserMadeReminder $reminders
     */
    public function removeReminder(\Ilios\CoreBundle\Entity\UserMadeReminder $reminders)
    {
        $this->reminders->removeElement($reminders);
    }

    /**
     * Get reminders
     *
     * @return \Ilios\CoreBundle\Entity\UserMadeReminder[]
     */
    public function getReminders()
    {
        return $this->reminders->toArray();
    }

    /**
     * Set primarySchool
     *
     * @param \Ilios\CoreBundle\Entity\School $primarySchool
     * @return User
     */
    public function setPrimarySchool(\Ilios\CoreBundle\Entity\School $primarySchool = null)
    {
        $this->primarySchool = $primarySchool;

        return $this;
    }

    /**
     * Get primarySchool
     *
     * @return \Ilios\CoreBundle\Entity\School
     */
    public function getPrimarySchool()
    {
        return $this->primarySchool;
    }

    /**
     * Get primarySchool ID
     *
     * @return integer
     */
    public function getPrimarySchoolId()
    {
        return $this->primarySchool->getSchoolId();
    }

    /**
     * Add directedCourses
     *
     * @param \Ilios\CoreBundle\Entity\Course $course
     * @return User
     */
    public function addDirectedCourse(\Ilios\CoreBundle\Entity\Course $course)
    {
        $this->directedCourses[] = $course;

        return $this;
    }

    /**
     * Remove directedCourses
     *
     * @param \Ilios\CoreBundle\Entity\Course $course
     */
    public function removeDirectedCourse(\Ilios\CoreBundle\Entity\Course $course)
    {
        $this->directedCourses->removeElement($course);
    }

    /**
     * Get directedCourses
     *
     * @return \Ilios\CoreBundle\Entity\Course[]
     */
    public function getDirectedCourses()
    {
        return $this->directedCourses->toArray();
    }

    /**
     * Add userGroups
     *
     * @param \Ilios\CoreBundle\Entity\Group $userGroups
     * @return User
     */
    public function addUserGroup(\Ilios\CoreBundle\Entity\Group $userGroups)
    {
        $this->userGroups[] = $userGroups;

        return $this;
    }

    /**
     * Remove userGroups
     *
     * @param \Ilios\CoreBundle\Entity\Group $userGroups
     */
    public function removeUserGroup(\Ilios\CoreBundle\Entity\Group $userGroups)
    {
        $this->userGroups->removeElement($userGroups);
    }

    /**
     * Get userGroups
     *
     * @return \Ilios\CoreBundle\Entity\Group[]
     */
    public function getUserGroups()
    {
        return $this->userGroups->toArray();
    }

    /**
     * Add instructorUserGroups
     *
     * @param \Ilios\CoreBundle\Entity\Group $instructorUserGroups
     * @return User
     */
    public function addInstructorUserGroup(\Ilios\CoreBundle\Entity\Group $instructorUserGroups)
    {
        $this->instructorUserGroups[] = $instructorUserGroups;

        return $this;
    }

    /**
     * Remove instructorUserGroups
     *
     * @param \Ilios\CoreBundle\Entity\Group $instructorUserGroups
     */
    public function removeInstructorUserGroup(\Ilios\CoreBundle\Entity\Group $instructorUserGroups)
    {
        $this->instructorUserGroups->removeElement($instructorUserGroups);
    }

    /**
     * Get instructorUserGroups
     *
     * @return \Ilios\CoreBundle\Entity\Group[]
     */
    public function getInstructorUserGroups()
    {
        return $this->instructorUserGroups->toArray();
    }

    /**
     * Add instructorGroups
     *
     * @param \Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups
     * @return User
     */
    public function addInstructorGroup(\Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups[] = $instructorGroups;

        return $this;
    }

    /**
     * Remove instructorGroups
     *
     * @param \Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups
     */
    public function removeInstructorGroup(\Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups->removeElement($instructorGroups);
    }

    /**
     * Get instructorGroups
     *
     * @return \Ilios\CoreBundle\Entity\InstructorGroup[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups->toArray();
    }

    /**
     * Add offerings
     *
     * @param \Ilios\CoreBundle\Entity\Offering $offerings
     * @return User
     */
    public function addOffering(\Ilios\CoreBundle\Entity\Offering $offerings)
    {
        $this->offerings[] = $offerings;

        return $this;
    }

    /**
     * Remove offerings
     *
     * @param \Ilios\CoreBundle\Entity\Offering $offerings
     */
    public function removeOffering(\Ilios\CoreBundle\Entity\Offering $offerings)
    {
        $this->offerings->removeElement($offerings);
    }

    /**
     * Get offerings
     *
     * @return \Ilios\CoreBundle\Entity\Offering[]
     */
    public function getOfferings()
    {
        return $this->offerings->toArray();
    }

    /**
     * Get offering IDs
     *
     * @return array
     */
    public function getOfferingIds()
    {
        $ids = array();
        foreach ($this->offerings as $offering) {
            $ids[] = $offering->getOfferingId();
        }

        return $ids;
    }

    /**
     * Add programYears
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYears
     * @return User
     */
    public function addProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYears)
    {
        $this->programYears[] = $programYears;

        return $this;
    }

    /**
     * Remove programYears
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYears
     */
    public function removeProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYears)
    {
        $this->programYears->removeElement($programYears);
    }

    /**
     * Get programYears
     *
     * @return \Ilios\CoreBundle\Entity\ProgramYear[]
     */
    public function getProgramYears()
    {
        return $this->programYears->toArray();
    }

    /**
     * Add alerts
     *
     * @param \Ilios\CoreBundle\Entity\Alert $alerts
     * @return User
     */
    public function addAlert(\Ilios\CoreBundle\Entity\Alert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \Ilios\CoreBundle\Entity\Alert $alerts
     */
    public function removeAlert(\Ilios\CoreBundle\Entity\Alert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Ilios\CoreBundle\Entity\Alert[]
     */
    public function getAlerts()
    {
        return $this->alerts->toArray();
    }

    /**
     * Add roles
     *
     * @param \Ilios\CoreBundle\Entity\UserRole $roles
     * @return User
     */
    public function addRole(\Ilios\CoreBundle\Entity\UserRole $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Ilios\CoreBundle\Entity\UserRole $roles
     */
    public function removeRole(\Ilios\CoreBundle\Entity\UserRole $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Ilios\CoreBundle\Entity\UserRole[]
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * Add learningMaterial
     *
     * @param \Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial
     * @return User
     */
    public function addLearningMaterial(\Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial)
    {
        $this->learningMaterials[] = $learningMaterial;

        return $this;
    }

    /**
     * Remove learningMaterial
     *
     * @param \Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial
     */
    public function removeLearningMaterial(\Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    /**
     * Get learningMaterials
     *
     * @return \Ilios\CoreBundle\Entity\LearningMaterial[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials->toArray();
    }

    /**
     * Add publishEvent
     *
     * @param \Ilios\CoreBundle\Entity\PublishEvent $publishEvent
     * @return User
     */
    public function addPublishEvent(\Ilios\CoreBundle\Entity\PublishEvent $publishEvent)
    {
        $this->publishEvents[] = $publishEvent;

        return $this;
    }

    /**
     * Remove publishEvent
     *
     * @param \Ilios\CoreBundle\Entity\PublishEvent $publishEvent
     */
    public function removePublishEvent(\Ilios\CoreBundle\Entity\PublishEvent $publishEvent)
    {
        $this->publishEvents->removeElement($publishEvent);
    }

    /**
     * Get publishEvents
     *
     * @return \Ilios\CoreBundle\Entity\PublishEvent[]
     */
    public function getPublishEvents()
    {
        return $this->publishEvents->toArray();
    }

    /**
     * Add report
     *
     * @param \Ilios\CoreBundle\Entity\Report $report
     * @return User
     */
    public function addReport(\Ilios\CoreBundle\Entity\Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param \Ilios\CoreBundle\Entity\Report $report
     */
    public function removeReport(\Ilios\CoreBundle\Entity\Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get reports
     *
     * @return \Ilios\CoreBundle\Entity\Report[]
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

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->userId;
    }
}
