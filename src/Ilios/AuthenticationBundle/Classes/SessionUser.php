<?php

namespace Ilios\AuthenticationBundle\Classes;

use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Ilios\CoreBundle\Entity\UserInterface as IliosUserInterface;
use Ilios\CoreBundle\Entity\UserRoleInterface;
use DateTime;

/**
 * Class SessionUser
 *
 * A session user is a static serializable representation
 * of a single user.  It is used in our authentication system
 * to avoid issues with a user being able to update their own data.
 */
class SessionUser implements SessionUserInterface
{
    /**
     * @var array
     */
    protected $roleTitles;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $schoolIds;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var bool
     */
    protected $isRoot;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var DateTime
     */
    protected $tokenNotValidBefore;

    /**
     * @var integer
     */
    protected $schoolId;

    /**
     * @var bool
     */
    protected $isLegacyAccount;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $directedCourseIds;

    /**
     * @var array
     */
    protected $administeredCourseIds;

    /**
     * @var array
     */
    protected $directedSchoolIds;

    /**
     * @var array
     */
    protected $administeredSchoolIds;

    /**
     * @var array
     */
    protected $directedCourseSchoolIds;

    /**
     * @var array
     */
    protected $administeredCourseSchoolIds;

    /**
     * @var array
     */
    protected $administeredSessionSchoolIds;

    /**
     * @var array
     */
    protected $administeredSessionCourseIds;

    /**
     * @var array
     */
    protected $taughtCourseIds;

    /**
     * @var array
     */
    protected $taughtCourseSchoolIds;
    
    public function __construct(IliosUserInterface $user)
    {
        $this->roleTitles = $user->getRoles()->map(function (UserRoleInterface $role) {
            return $role->getTitle();
        })->toArray();

        $this->schoolIds = $user->getAllSchools()->map(function (SchoolInterface $school) {
            return $school->getId();
        })->toArray();

        $this->directedCourseIds = $user->getDirectedCourses()->map(function (CourseInterface $course) {
            return $course->getId();
        })->toArray();

        $this->administeredCourseIds = $user->getAdministeredCourses()->map(function (CourseInterface $course) {
            return $course->getId();
        })->toArray();

        $this->directedSchoolIds = $user->getDirectedSchools()->map(function (SchoolInterface $school) {
            return $school->getId();
        })->toArray();

        $this->administeredSchoolIds = $user->getAdministeredSchools()->map(function (SchoolInterface $school) {
            return $school->getId();
        })->toArray();

        $this->directedCourseSchoolIds = $user->getDirectedCourses()->map(function (CourseInterface $course) {
            return $course->getSchool()->getId();
        })->toArray();

        $this->administeredCourseSchoolIds = $user->getAdministeredCourses()->map(function (CourseInterface $course) {
            return $course->getSchool()->getId();
        })->toArray();

        $this->administeredSessionSchoolIds = $user->getAdministeredSessions()
            ->map(function (SessionInterface $session) {
                return $session->getCourse()->getSchool()->getId();
            })->toArray();

        $this->administeredSessionCourseIds = $user->getAdministeredSessions()
            ->map(function (SessionInterface $session) {
                return $session->getCourse()->getId();
            })->toArray();

        $this->taughtCourseIds = $user->getInstructedCourses()->map(function (CourseInterface $course) {
            return $course->getId();
        })->toArray();

        $this->taughtCourseSchoolIds = $user->getInstructedCourses()->map(function (CourseInterface $course) {
            return $course->getSchool()->getId();
        })->toArray();

        $this->userId = $user->getId();
        $this->isRoot = $user->isRoot();
        $this->isEnabled = $user->isEnabled();
        $this->schoolId = $user->getSchool()->getId();

        $authentication = $user->getAuthentication();
        if ($authentication) {
            $this->tokenNotValidBefore = $authentication->getInvalidateTokenIssuedBefore();
            $this->password = $authentication->getPassword();
            $this->isLegacyAccount = $authentication->isLegacyAccount();
        }

        $permissions = [];
        foreach ($user->getPermissions() as $permission) {
            $name = $permission->getTableName();
            $id = $permission->getTableRowId();
            if (!array_key_exists($name, $permissions)) {
                $permissions[$name] = [];
            }
            if (!array_key_exists($id, $permissions[$name])) {
                $permissions[$name][$id] = [
                    'canRead' => false,
                    'canWrite' => false
                ];
            }
            if ($permission->hasCanRead()) {
                $permissions[$name][$id]['canRead'] = true;
            }
            if ($permission->hasCanWrite()) {
                $permissions[$name][$id]['canWrite'] = true;
            }
        }

        $this->permissions = $permissions;
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof SessionUser) {
            return false;
        }

        if ($this->userId === $user->getUsername()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isTheUser(IliosUserInterface $user)
    {

        if ($this->userId === $user->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isThePrimarySchool(SchoolInterface $school)
    {

        if ($this->schoolId === $school->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->userId;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        $this->password = null;
    }

    /**
     * @inheritdoc
     */
    public function hasRole(array $eligibleRoles)
    {
        $intersection = array_intersect($eligibleRoles, $this->roleTitles);

        return ! empty($intersection);
    }

    /**
     * @inheritdoc
     */
    public function isRoot()
    {
        return $this->isRoot;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @inheritDoc
     */
    public function tokenNotValidBefore()
    {
        return $this->tokenNotValidBefore;
    }

    /**
     * @inheritDoc
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function hasReadPermissionToSchool($schoolId)
    {
        return $this->canRead('school', $schoolId);
    }

    /**
     * @inheritdoc
     */
    public function hasReadPermissionToSchools(array $schoolIds)
    {
        foreach ($schoolIds as $id) {
            if ($this->canRead('school', $id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function hasReadPermissionToProgram($programId)
    {
        return $this->canRead('program', $programId);
    }

    /**
     * @inheritdoc
     */
    public function hasReadPermissionToCourse($courseId)
    {
        return $this->canRead('course', $courseId);
    }

    /**
     * @inheritdoc
     */
    public function hasWritePermissionToSchool($schoolId)
    {
        return $this->canWrite('school', $schoolId);
    }

    /**
     * @inheritdoc
     */
    public function hasWritePermissionToSchools(array $schoolIds)
    {
        foreach ($schoolIds as $id) {
            if ($this->canWrite('school', $id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function hasWritePermissionToProgram($programId)
    {
        return $this->canWrite('program', $programId);
    }

    /**
     * @inheritdoc
     */
    public function hasWritePermissionToCourse($courseId)
    {
        return $this->canWrite('course', $courseId);
    }

    /**
     * Can a user read something?
     *
     * @param $type
     * @param $id
     * @return bool
     */
    protected function canRead($type, $id)
    {
        if (array_key_exists($type, $this->permissions)) {
            if (array_key_exists($id, $this->permissions[$type])) {
                $permission = $this->permissions[$type][$id];
                $canRead = array_key_exists('canRead', $permission)?$permission['canRead']:false;
                $canWrite = array_key_exists('canWrite', $permission)?$permission['canWrite']:false;

                return $canRead || $canWrite;
            }
        }

        return false;
    }

    /**
     * Can a user write something
     *
     * @param $type
     * @param $id
     * @return bool
     */
    protected function canWrite($type, $id)
    {
        if (array_key_exists($type, $this->permissions)) {
            if (array_key_exists($id, $this->permissions[$type])) {
                $permission = $this->permissions[$type][$id];
                $canWrite = array_key_exists('canWrite', $permission)?$permission['canWrite']:false;

                return $canWrite;
            }
        }

        return false;
    }

    /**
     * Use the old ilios legacy encoder for accounts
     * that haven't changed their password
     * @return string|null
     */
    public function getEncoderName()
    {
        if ($this->isLegacyAccount) {
            return 'ilios_legacy_encoder';
        }

        return null; // use the default encoder
    }

    /**
     * @inheritdoc
     */
    public function isDirectingCourse(int $courseId)
    {
        return in_array($courseId, $this->directedCourseIds);
    }

    public function isAdministeringCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->administeredCourseIds);
    }

    public function isDirectingSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->directedSchoolIds);
    }

    public function isAdministeringSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredSchoolIds);
    }

    public function isDirectingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->directedCourseSchoolIds);
    }

    public function isAdministeringCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredCourseSchoolIds);
    }

    public function isAdministeringSessionInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredSessionSchoolIds);
    }

    public function isAdministeringSessionInCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->administeredSessionCourseIds);
    }

    public function isTeachingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->taughtCourseSchoolIds);
    }

    public function isTeachingCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->taughtCourseIds);
    }

    public function rolesInSchool(int $schoolId) : array
    {
        $roles = [];
        if ($this->isDirectingSchool($schoolId)) {
            $roles[] = UserRoles::SCHOOL_DIRECTOR;
        }
        if ($this->isAdministeringSchool($schoolId)) {
            $roles[] = UserRoles::SCHOOL_ADMINISTRATOR;
        }
        if ($this->isDirectingCourseInSchool($schoolId)) {
            $roles[] = UserRoles::COURSE_DIRECTOR;
        }
        if ($this->isAdministeringCourseInSchool($schoolId)) {
            $roles[] = UserRoles::COURSE_ADMINISTRATOR;
        }
        if ($this->isAdministeringSessionInSchool($schoolId)) {
            $roles[] = UserRoles::SESSION_ADMINISTRATOR;
        }
        if ($this->isTeachingCourseInSchool($schoolId)) {
            $roles[] = UserRoles::COURSE_INSTRUCTOR;
        }

        return $roles;
    }

    public function rolesInCourse(int $courseId) : array
    {
        $roles = [];

        if ($this->isDirectingCourse($courseId)) {
            $roles[] = UserRoles::COURSE_DIRECTOR;
        }
        if ($this->isAdministeringCourse($courseId)) {
            $roles[] = UserRoles::COURSE_ADMINISTRATOR;
        }
        if ($this->isAdministeringSessionInCourse($courseId)) {
            $roles[] = UserRoles::SESSION_ADMINISTRATOR;
        }
        if ($this->isTeachingCourse($courseId)) {
            $roles[] = UserRoles::COURSE_INSTRUCTOR;
        }

        return $roles;
    }
}
