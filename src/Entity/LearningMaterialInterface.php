<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use DateTime;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableNullableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledEntityInterface;

interface LearningMaterialInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    DescribableNullableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface,
    IndexableCoursesEntityInterface
{
    public function setOriginalAuthor(?string $originalAuthor);
    public function getOriginalAuthor(): ?string;

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
    public function getOwningUser(): UserInterface;

    public function setCitation(?string $citation);
    public function getCitation(): ?string;

    public function setLink(?string $link);
    public function getLink(): ?string;

    public function setRelativePath(?string $path);
    public function getRelativePath(): ?string;

    /**
     * @param ?bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission);

    public function hasCopyrightPermission(): ?bool;

    public function setCopyrightRationale(?string $copyrightRationale);
    public function getCopyrightRationale(): ?string;

    public function getUploadDate(): DateTime;

    public function setMimetype(?string $mimetype);
    public function getMimetype(): ?string;

    public function setFilesize(?int $filesize);
    public function getFilesize(): ?int;


    public function setFilename(?string $filename);
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
    public function getOwningSchool(): SchoolInterface;

    /**
     * Use the data in the object to determine which validation
     * groups should be applied
     */
    public function getValidationGroups(): array;
}
