<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class UserEvent
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */
class LearningMaterialDecorator
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("uploadDate")
     */
    protected $uploadDate;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("originalAuthor")
     */
    protected $originalAuthor;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("userRole")
     */
    protected $userRole;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $status;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("owningUser")
     */
    protected $owningUser;

    /**
     * @var string[]
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionLearningMaterials")
     */
    protected $sessionLearningMaterials;

    /**
     * @var string[]
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("courseLearningMaterials")
     */
    protected $courseLearningMaterials;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $citation;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("copyrightPermission")
     */
    protected $copyrightPermission;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("copyrightRationale")
     */
    protected $copyrightRationale;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $filename;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $mimetype;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $filesize;


    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $link;


    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("absoluteFileUri")
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
