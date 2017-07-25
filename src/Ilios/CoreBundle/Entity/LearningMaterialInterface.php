<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialInterface
 */
interface LearningMaterialInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    DescribableEntityInterface,
    LoggableEntityInterface,
    SessionStampableInterface
{

    /**
     * @param string $orignalAuthor
     */
    public function setOriginalAuthor($orignalAuthor);

    /**
     * @return string
     */
    public function getOriginalAuthor();

    /**
     * @return string
     */
    public function getToken();
    
    /**
     * Generate a random token for use in downloading
     */
    public function generateToken();

    /**
     * @param LearningMaterialStatusInterface $status
     */
    public function setStatus(LearningMaterialStatusInterface $status);

    /**
     * @return LearningMaterialStatusInterface
     */
    public function getStatus();

    /**
     * @param LearningMaterialUserRoleInterface $userRole
     */
    public function setUserRole(LearningMaterialUserRoleInterface $userRole);

    /**
     * @return LearningMaterialUserRoleInterface
     */
    public function getUserRole();

    /**
     * @param UserInterface $user
     */
    public function setOwningUser(UserInterface $user);

    /**
     * @return UserInterface|null
     */
    public function getOwningUser();
    
    /**
     * @param string $text
     */
    public function setCitation($citation);

    /**
     * @return string
     */
    public function getCitation();
    
    /**
     * @param string $link
     */
    public function setLink($link);

    /**
     * @return string
     */
    public function getLink();
    
    /**
     * @param string $path
     */
    public function setRelativePath($path);

    /**
     * @return string
     */
    public function getRelativePath();

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission);

    /**
     * @return bool
     */
    public function hasCopyrightPermission();

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale);

    /**
     * @return string
     */
    public function getCopyrightRationale();

    /**
     * @return string
     */
    public function getUploadDate();

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype);

    /**
     * @return string
     */
    public function getMimetype();

    /**
     * @param string $filesize
     */
    public function setFilesize($filesize);

    /**
     * @return string
     */
    public function getFilesize();


    /**
     * @param string $filename
     */
    public function setFilename($filename);

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param Collection $courseLearningMaterials
     */
    public function setCourseLearningMaterials(Collection $courseLearningMaterials = null);

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials();

    /**
     * @param Collection $sessionLearningMaterials
     */
    public function setSessionLearningMaterials(Collection $sessionLearningMaterials = null);

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getSessionLearningMaterials();

    /**
     * Gets the primary school of the LM's owning user.
     * @return SchoolInterface|null
     */
    public function getOwningSchool();

    /**
     * Use the data in the object to determine which validation
     * groups should be applied
     * @return array
     */
    public function getValidationGroups();
}
