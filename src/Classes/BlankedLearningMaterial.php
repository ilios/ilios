<?php

namespace App\Classes;

use App\Entity\CourseLearningMaterialInterface;
use App\Entity\LearningMaterial;
use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialStatusInterface;
use App\Entity\LearningMaterialUserRoleInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;
use Exception;

/**
 * Read-only facade to the Learning Material entity that "blanks" most of its attributes.
 *
 * Class BlankedLearningMaterial
 * @package App\Classes
 */
class BlankedLearningMaterial implements LearningMaterialInterface
{
    /**
     * @var LearningMaterial $material
     */
    protected $material;
    /**
     * BlankedLearningMaterial constructor.
     * @param LearningMaterial $material
     */
    public function __construct(LearningMaterial $material)
    {
        $this->material = $material;
    }

    /**
     * @param string $description
     * @throws Exception
     */
    public function setDescription($description)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return null;
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function setId($id)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->material->getId();
    }

    /**
     * @inheritdoc
     */
    public function getIndexableCourses(): array
    {
        return $this->material->getIndexableCourses();
    }

    /**
     * @param string $orignalAuthor
     * @throws Exception
     */
    public function setOriginalAuthor($orignalAuthor)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getOriginalAuthor()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return null;
    }

    /**
     * @throws Exception
     */
    public function generateToken()
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @param LearningMaterialStatusInterface $status
     * @throws Exception
     */
    public function setStatus(LearningMaterialStatusInterface $status)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->material->getStatus();
    }

    /**
     * @param LearningMaterialUserRoleInterface $userRole
     * @throws Exception
     */
    public function setUserRole(LearningMaterialUserRoleInterface $userRole)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getUserRole()
    {
        return $this->material->getUserRole();
    }

    /**
     * @param UserInterface $user
     * @throws Exception
     */
    public function setOwningUser(UserInterface $user)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getOwningUser()
    {
        return $this->material->getOwningUser();
    }

    /**
     * @param string $citation
     * @throws Exception
     */
    public function setCitation($citation)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getCitation()
    {
        return null;
    }

    /**
     * @param string $link
     * @throws Exception
     */
    public function setLink($link)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getLink()
    {
        return null;
    }

    /**
     * @param string $path
     * @throws Exception
     */
    public function setRelativePath($path)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getRelativePath()
    {
        return null;
    }

    /**
     * @param bool $copyrightPermission
     * @throws Exception
     */
    public function setCopyrightPermission($copyrightPermission)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function hasCopyrightPermission()
    {
        return $this->material->hasCopyrightPermission();
    }

    /**
     * @param string $copyrightRationale
     * @throws Exception
     */
    public function setCopyrightRationale($copyrightRationale)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getCopyrightRationale()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUploadDate()
    {
        return $this->material->getUploadDate();
    }

    /**
     * @param string $mimetype
     * @throws Exception
     */
    public function setMimetype($mimetype)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getMimetype()
    {
        return null;
    }

    /**
     * @param string $filesize
     * @throws Exception
     */
    public function setFilesize($filesize)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getFilesize()
    {
        return null;
    }

    /**
     * @param string $filename
     * @throws Exception
     */
    public function setFilename($filename)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @return null
     */
    public function getFilename()
    {
        return null;
    }

    /**
     * @param Collection $courseLearningMaterials
     * @throws Exception
     */
    public function setCourseLearningMaterials(Collection $courseLearningMaterials = null)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @throws Exception
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @throws Exception
     */
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getCourseLearningMaterials()
    {
        return $this->material->getCourseLearningMaterials();
    }

    /**
     * @param Collection $sessionLearningMaterials
     * @throws Exception
     */
    public function setSessionLearningMaterials(Collection $sessionLearningMaterials = null)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @throws Exception
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @throws Exception
     */
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getSessionLearningMaterials()
    {
        return $this->material->getSessionLearningMaterials();
    }

    /**
     * @inheritdoc
     */
    public function getOwningSchool()
    {
        return $this->material->getOwningSchool();
    }

    /**
     * @inheritdoc
     */
    public function getValidationGroups()
    {
        return $this->material->getValidationGroups();
    }

    /**
     * @inheritdoc
     */
    public function getSessions()
    {
        return $this->material->getSessions();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->material->__toString();
    }

    /**
     * @param string $title
     * @throws Exception
     */
    public function setTitle($title)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->material->getTitle();
    }
}
