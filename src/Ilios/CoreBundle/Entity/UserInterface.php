<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Interface UserInterface
 * @package Ilios\CoreBundle\Entity
 */
interface UserInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
    OfferingsEntityInterface,
    ProgramYearsEntityInterface,
    LoggableEntityInterface,
    BaseUserInterface,
    SchoolEntityInterface,
    EncoderAwareInterface,
    \Serializable
{
    /**
     * @param AuthenticationInterface|null $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication = null);


    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName);

    /**
     * @return string
     */
    public function getMiddleName();

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param boolean $addedViaIlios
     */
    public function setAddedViaIlios($addedViaIlios);

    /**
     * @return boolean
     */
    public function isAddedViaIlios();

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return boolean
     */
    public function isEnabled();

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId);

    /**
     * @return string
     */
    public function getCampusId();

    /**
     * @param string $otherId
     */
    public function setOtherId($otherId);

    /**
     * @return string
     */
    public function getOtherId();

    /**
     * @param boolean $examined
     */
    public function setExamined($examined);

    /**
     * @return boolean
     */
    public function isExamined();

    /**
     * @param boolean $userSyncIgnore
     */
    public function setUserSyncIgnore($userSyncIgnore);

    /**
     * @return boolean
     */
    public function isUserSyncIgnore();
    
    /**
     * Generate a random string to use as the calendar feed url
     */
    public function generateIcsFeedKey();

    /**
     * Sets the ICS Feed Key
     * @param string $icsFeedKey
     */
    public function setIcsFeedKey($icsFeedKey);

    /**
     * @return string
     */
    public function getIcsFeedKey();

    /**
     * @param Collection $reminders
     */
    public function setReminders(Collection $reminders);

    /**
     * @param UserMadeReminderInterface $reminder
     */
    public function addReminder(UserMadeReminderInterface $reminder);

    /**
     * @return ArrayCollection|UserMadeReminderInterface[]
     */
    public function getReminders();

    /**
     * @param Collection $courses
     */
    public function setDirectedCourses(Collection $courses);

    /**
     * @param CourseInterface $course
     */
    public function addDirectedCourse(CourseInterface $course);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDirectedCourses();

    /**
     * @param Collection $userGroups
     */
    public function setLearnerGroups(Collection $userGroups);

    /**
     * @param LearnerGroupInterface $userGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $userGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups();

    /**
     * @param Collection $instructedLearnerGroups
     */
    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups);

    /**
     * @param LearnerGroupInterface $instructedLearnerGroup
     */
    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getInstructedLearnerGroups();

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups();

    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts);

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();

    /**
     * @param Collection $roles
     */
    public function setRoles(Collection $roles);

    /**
     * @param UserRoleInterface $role
     */
    public function addRole(UserRoleInterface $role);

    /**
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function getRoles();

    /**
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials);

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
     * @return ArrayCollection|LearningMaterialInterface[]
     */
    public function getLearningMaterials();

    /**
     * @param Collection $publishEvents
     */
    public function setPublishEvents(Collection $publishEvents);

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function addPublishEvent(PublishEventInterface $publishEvent);

    /**
     * @return ArrayCollection|PublishEventInterface[]
     */
    public function getPublishEvents();

    /**
     * @param Collection $reports
     */
    public function setReports(Collection $reports);

    /**
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report);

    /**
     * @return ArrayCollection|ReportInterface[]
     */
    public function getReports();

    /**
     * @param Collection $pendingUserUpdates
     */
    public function setPendingUserUpdates(Collection $pendingUserUpdates);

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     */
    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    /**
     * @return ArrayCollection|PendingUserUpdateInterface[]
     */
    public function getPendingUserUpdates();

    /**
     * @param Collection $cohorts
     */
    public function setCohorts(Collection $cohorts);

    /**
     * @param CohortInterface $cohort
     */
    public function addCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface[]|ArrayCollection
     */
    public function getCohorts();

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
     * @return ArrayCollection[School]
     */
    public function getAllSchools();

    /**
     * @param Collection $permissions
     */
    public function setPermissions(Collection $permissions);

    /**
     * @param PermissionInterface $permission
     */
    public function addPermission(PermissionInterface $permission);

    /**
     * @return ArrayCollection|PermissionInterface[]
     */
    public function getPermissions();
}
