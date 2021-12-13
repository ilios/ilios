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

    public function getOriginalAuthor(): string;

    public function getToken(): string;

    /**
     * Generate a random token for use in downloading
     */
    public function generateToken();

    public function setStatus(LearningMaterialStatusInterface $status);

    public function getStatus(): LearningMaterialStatusInterface;

    public function setUserRole(LearningMaterialUserRoleInterface $userRole);

    public function getUserRole(): LearningMaterialUserRoleInterface;

    public function setOwningUser(UserInterface $user);

    public function getOwningUser(): ?UserInterface;

    /**
     * @param string $citation
     */
    public function setCitation($citation);

    public function getCitation(): ?string;

    /**
     * @param string $link
     */
    public function setLink($link);

    public function getLink(): ?string;

    /**
     * @param string $path
     */
    public function setRelativePath($path);

    public function getRelativePath(): ?string;

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission);

    public function hasCopyrightPermission(): ?bool;

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale);

    public function getCopyrightRationale(): ?string;

    public function getUploadDate(): DateTime;

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype);

    public function getMimetype(): ?string;

    /**
     * @param string $filesize
     */
    public function setFilesize($filesize);

    public function getFilesize(): ?string;


    /**
     * @param string $filename
     */
    public function setFilename($filename);

    public function getFilename(): ?string;

    public function setCourseLearningMaterials(Collection $courseLearningMaterials = null);

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    public function getCourseLearningMaterials(): Collection;

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials = null);

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    public function getSessionLearningMaterials(): Collection;

    /**
     * Gets the primary school of the LM's owning user.
     */
    public function getOwningSchool(): ?SchoolInterface;

    /**
     * Use the data in the object to determine which validation
     * groups should be applied
     */
    public function getValidationGroups(): array;
}
