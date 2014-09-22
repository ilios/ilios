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

interface UserInterface
{
    /**
     * @return integer
     */
    public function getUserId();

    /**
     * @param integer $userId
     */
    public function setUserId($userId);

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName);

    /**
     * @return string
     */
    public function getMiddleName();

    /**
     * @param string $phone
     * @return User
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param boolean $addedViaIlios
     * @return User
     */
    public function setAddedViaIlios($addedViaIlios);

    /**
     * @return boolean
     */
    public function getAddedViaIlios();

    /**
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled);

    /**
     * @return boolean
     */
    public function getEnabled();

    /**
     * @param string $ucUid
     * @return User
     */
    public function setUcUid($ucUid);

    /**
     * @return string
     */
    public function getUcUid();

    /**
     * @param string $otherId
     * @return User
     */
    public function setOtherId($otherId);

    /**
     * @return string
     */
    public function getOtherId();

    /**
     * @param boolean $examined
     * @return User
     */
    public function setExamined($examined);

    /**
     * @return boolean
     */
    public function getExamined();

    /**
     * @param boolean $userSyncIgnore
     * @return User
     */
    public function setUserSyncIgnore($userSyncIgnore);

    /**
     * @return boolean
     */
    public function getUserSyncIgnore();

    /**
     * @param ApiKey $apiKey
     * @return User
     */
    public function setApiKey(ApiKey $apiKey = null);

    /**
     * @return ApiKey
     */
    public function getApiKey();

    /**
     * @param UserMadeReminder $reminders
     * @return User
     */
    public function addReminder(UserMadeReminder $reminders);

    /**
     * @param UserMadeReminder $reminders
     */
    public function removeReminder(UserMadeReminder $reminders);

    /**
     * @return UserMadeReminder[]
     */
    public function getReminders();

    /**
     * @param School $primarySchool
     * @return User
     */
    public function setPrimarySchool(School $primarySchool = null);

    /**
     * @return School
     */
    public function getPrimarySchool();

    /**
     * @param Course $course
     * @return User
     */
    public function addDirectedCourse(Course $course);

    /**
     * @param Course $course
     */
    public function removeDirectedCourse(Course $course);

    /**
     * @return Course[]
     */
    public function getDirectedCourses();

    /**
     * @param Group $userGroups
     * @return User
     */
    public function addUserGroup(Group $userGroups);

    /**
     * @param Group $userGroups
     */
    public function removeUserGroup(Group $userGroups);

    /**
     * @return Group[]
     */
    public function getUserGroups();

    /**
     * @param Group $instructorUserGroups
     * @return User
     */
    public function addInstructorUserGroup(Group $instructorUserGroups);

    /**
     * @param Group $instructorUserGroups
     */
    public function removeInstructorUserGroup(Group $instructorUserGroups);

    /**
     * @return Group[]
     */
    public function getInstructorUserGroups();

    /**
     * @param InstructorGroup $instructorGroups
     * @return User
     */
    public function addInstructorGroup(InstructorGroup $instructorGroups);

    /**
     * @param InstructorGroup $instructorGroups
     */
    public function removeInstructorGroup(InstructorGroup $instructorGroups);

    /**
     * @return InstructorGroup[]
     */
    public function getInstructorGroups();

    /**
     * @param Offering $offerings
     * @return User
     */
    public function addOffering(Offering $offerings);

    /**
     * @param Offering $offerings
     */
    public function removeOffering(Offering $offerings);

    /**
     * @return Offering[]
     */
    public function getOfferings();

    /**
     * @param ProgramYear $programYears
     * @return User
     */
    public function addProgramYear(ProgramYear $programYears);

    /**
     * @param ProgramYear $programYears
     */
    public function removeProgramYear(ProgramYear $programYears);

    /**
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

    /**
     * @inheritDoc
     */
    public function serialize();

    /**
     * @inheritDoc
     */
    public function unserialize($serialized);

    /**
     * @inheritDoc
     */
    public function eraseCredentials();

    /**
     * @inheritDoc
     */
    public function getPassword();

    /**
     * @return string
     */
    public function getSalt();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function __toString();
}
