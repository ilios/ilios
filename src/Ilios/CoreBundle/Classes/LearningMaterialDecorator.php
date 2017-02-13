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
 * @IS\Entity
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
     * @IS\Type("string")
     */
    protected $userRole;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $status;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
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
}
