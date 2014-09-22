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
 * Interface UserInterface
 */
interface UserInterface 
{
    public function getUserId();

    public function setUserId($userId);

    public function setLastName($lastName);

    public function getLastName();

    public function setFirstName($firstName);

    public function getFirstName();

    public function setMiddleName($middleName);

    public function getMiddleName();

    public function setPhone($phone);

    public function getPhone();

    public function setEmail($email);

    public function getEmail();

    public function setAddedViaIlios($addedViaIlios);

    public function getAddedViaIlios();

    public function setEnabled($enabled);

    public function getEnabled();

    public function setUcUid($ucUid);

    public function getUcUid();

    public function setOtherId($otherId);

    public function getOtherId();

    public function setExamined($examined);

    public function getExamined();

    public function setUserSyncIgnore($userSyncIgnore);

    public function getUserSyncIgnore();

    public function setApiKey(ApiKey $apiKey = null);

    public function getApiKey();

    public function addReminder(UserMadeReminder $reminders);

    public function removeReminder(UserMadeReminder $reminders);

    public function getReminders();

    public function setPrimarySchool(School $primarySchool = null);

    public function getPrimarySchool();

    public function addDirectedCourse(Course $course);

    public function removeDirectedCourse(Course $course);

    public function getDirectedCourses();

    public function addUserGroup(Group $userGroups);

    public function removeUserGroup(Group $userGroups);

    public function getUserGroups();

    public function addInstructorUserGroup(Group $instructorUserGroups);

    public function removeInstructorUserGroup(Group $instructorUserGroups);

    public function getInstructorUserGroups();

    public function addInstructorGroup(InstructorGroup $instructorGroups);

    public function removeInstructorGroup(InstructorGroup $instructorGroups);

    public function getInstructorGroups();

    public function addOffering(Offering $offerings);

    public function removeOffering(Offering $offerings);

    public function getOfferings();

    public function addProgramYear(ProgramYear $programYears);

    public function removeProgramYear(ProgramYear $programYears);

    public function getProgramYears();

    public function addAlert(Alert $alerts);

    public function removeAlert(Alert $alerts);

    public function getAlerts();

    public function addRole(UserRole $roles);

    public function removeRole(UserRole $roles);

    public function getRoles();

    public function addLearningMaterial(LearningMaterial $learningMaterial);

    public function removeLearningMaterial(LearningMaterial $learningMaterial);

    public function getLearningMaterials();

    public function addPublishEvent(PublishEvent $publishEvent);

    public function removePublishEvent(PublishEvent $publishEvent);

    public function getPublishEvents();

    public function addReport(Report $report);

    public function removeReport(Report $report);

    public function getReports();

    public function serialize();

    public function unserialize($serialized);

    public function eraseCredentials();

    public function getPassword();

    public function getSalt();

    public function getUsername();

    public function __toString();
}
