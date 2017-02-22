<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class UserEvent
 * @package Ilios\CoreBundle\Classes
 *
 * @IS\DTO
 */
class LearningMaterialDecorator
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $uploadDate;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $originalAuthor;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $userRole;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $status;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $owningUser;

    /**
     * @var string[]
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionLearningMaterials;

    /**
     * @var string[]
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $courseLearningMaterials;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $citation;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $copyrightPermission;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $copyrightRationale;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $filename;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $mimetype;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $filesize;


    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $link;


    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $absoluteFileUri;

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param Router $router
     */
    public function __construct(LearningMaterialInterface $learningMaterial, Router $router)
    {
        if ($learningMaterial->getFilename()) {
            $link = $router->generate(
                'ilios_core_downloadlearningmaterial',
                ['token' => $learningMaterial->getToken()],
                UrlGenerator::ABSOLUTE_URL
            );
            $this->absoluteFileUri = $link;
        }

        $this->id = $learningMaterial->getId();
        $this->title = $learningMaterial->getTitle();
        $this->description = $learningMaterial->getDescription();
        $this->uploadDate = $learningMaterial->getUploadDate();
        $this->originalAuthor = $learningMaterial->getOriginalAuthor();
        $this->userRole = (string) $learningMaterial->getUserRole();
        $this->status = (string) $learningMaterial->getStatus();
        $this->owningUser = (string) $learningMaterial->getOwningUser();
        $this->citation = $learningMaterial->getCitation();
        $this->copyrightPermission = $learningMaterial->hasCopyrightPermission();
        $this->copyrightRationale = $learningMaterial->getCopyrightRationale();
        $this->mimetype = $learningMaterial->getMimetype();
        $this->filesize = $learningMaterial->getFilesize();
        $this->filename = $learningMaterial->getFilename();
        $this->link = $learningMaterial->getLink();

        $courseLearningMaterialIds = $learningMaterial->getCourseLearningMaterials()
            ->map(function (CourseLearningMaterialInterface $lm) {
                return (string) $lm;
            });
        $this->courseLearningMaterials = $courseLearningMaterialIds->toArray();

        $sessionLearningMaterialIds = $learningMaterial->getSessionLearningMaterials()
            ->map(function (SessionLearningMaterialInterface $lm) {
                return (string) $lm;
            });
        $this->sessionLearningMaterials = $sessionLearningMaterialIds->toArray();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * @return string
     */
    public function getOriginalAuthor()
    {
        return $this->originalAuthor;
    }

    /**
     * @return string
     */
    public function getUserRole()
    {
        return $this->userRole;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getOwningUser()
    {
        return $this->owningUser;
    }

    /**
     * @return \string[]
     */
    public function getSessionLearningMaterials()
    {
        return $this->sessionLearningMaterials;
    }

    /**
     * @return \string[]
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials;
    }

    /**
     * @return string
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * @return bool
     */
    public function isCopyrightPermission()
    {
        return $this->copyrightPermission;
    }

    /**
     * @return string
     */
    public function getCopyrightRationale()
    {
        return $this->copyrightRationale;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @return string
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getAbsoluteFileUri()
    {
        return $this->absoluteFileUri;
    }
}
