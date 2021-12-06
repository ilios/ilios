<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialInterface
 */
interface LearningMaterialInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    DescribableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    IndexableCoursesEntityInterface
{

    /**
     * @param string $orignalAuthor
     */
    public function setOriginalAuthor($orignalAuthor);

    /**
     * @return string
     */
    public function getOriginalAuthor(): string;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * Generate a random token for use in downloading
     */
    public function generateToken();

    public function setStatus(LearningMaterialStatusInterface $status);

    /**
     * @return LearningMaterialStatusInterface
     */
    public function getStatus(): LearningMaterialStatusInterface;

    public function setUserRole(LearningMaterialUserRoleInterface $userRole);

    /**
     * @return LearningMaterialUserRoleInterface
     */
    public function getUserRole(): LearningMaterialUserRoleInterface;

    public function setOwningUser(UserInterface $user);

    /**
     * @return UserInterface|null
     */
    public function getOwningUser(): ?UserInterface;

    /**
     * @param string $citation
     */
    public function setCitation($citation);

    /**
     * @return string
     */
    public function getCitation(): string;

    /**
     * @param string $link
     */
    public function setLink($link);

    /**
     * @return string
     */
    public function getLink(): string;

    /**
     * @param string $path
     */
    public function setRelativePath($path);

    /**
     * @return string
     */
    public function getRelativePath(): string;

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission);

    /**
     * @return bool
     */
    public function hasCopyrightPermission(): bool;

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale);

    /**
     * @return string
     */
    public function getCopyrightRationale(): string;

    public function getUploadDate(): DateTime;

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype);

    /**
     * @return string
     */
    public function getMimetype(): string;

    /**
     * @param string $filesize
     */
    public function setFilesize($filesize);

    /**
     * @return string
     */
    public function getFilesize(): string;


    /**
     * @param string $filename
     */
    public function setFilename($filename);

    /**
     * @return string
     */
    public function getFilename(): string;

    public function setCourseLearningMaterials(Collection $courseLearningMaterials = null);

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials(): Collection;

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials = null);

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getSessionLearningMaterials(): Collection;

    /**
     * Gets the primary school of the LM's owning user.
     * @return SchoolInterface|null
     */
    public function getOwningSchool(): ?SchoolInterface;

    /**
     * Use the data in the object to determine which validation
     * groups should be applied
     * @return array
     */
    public function getValidationGroups(): array;
}
