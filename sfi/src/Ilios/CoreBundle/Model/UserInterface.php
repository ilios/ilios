<?php

namespace Ilios\CoreBundle\Model;

interface UserInterface
{
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
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getProgramYears();

    /**
     * @param Alert $alerts
     * @return User
     */
    public function addAlert(Alert $alerts);

    /**
     * @param Alert $alerts
     */
    public function removeAlert(Alert $alerts);

    /**
     * @return Alert[]
     */
    public function getAlerts();

    /**
     * @param UserRole $roles
     * @return User
     */
    public function addRole(UserRole $roles);

    /**
     * @param UserRole $roles
     */
    public function removeRole(UserRole $roles);

    /**
     * @return UserRole[]
     */
    public function getRoles();

    /**
     * @param LearningMaterial $learningMaterial
     * @return User
     */
    public function addLearningMaterial(LearningMaterial $learningMaterial);

    /**
     * @param LearningMaterial $learningMaterial
     */
    public function removeLearningMaterial(LearningMaterial $learningMaterial);

    /**
     * @return LearningMaterial[]
     */
    public function getLearningMaterials();

    /**
     * @param PublishEvent $publishEvent
     * @return User
     */
    public function addPublishEvent(PublishEvent $publishEvent);

    /**
     * @param PublishEvent $publishEvent
     */
    public function removePublishEvent(PublishEvent $publishEvent);

    /**
     * @return PublishEvent[]
     */
    public function getPublishEvents();

    /**
     * @param Report $report
     * @return User
     */
    public function addReport(Report $report);

    /**
     * @param Report $report
     */
    public function removeReport(Report $report);

    /**
     * @return Report[]
     */
    public function getReports();
}
