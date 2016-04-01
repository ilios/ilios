<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class MeshDescriptorDTO
 * Data transfer object for a MeSH descriptor.
 * @package Ilios\CoreBundle\Entity\DTO

 */
class MeshDescriptorDTO
{
    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $name;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    public $annotation;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("createdAt")
     */
    public $createdAt;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("updatedAt")
     */
    public $updatedAt;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $sessions;

    /**
     * @var string[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $concepts;

    /**
     * @var string[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $qualifiers;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $trees;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionLearningMaterials")
     */
    public $sessionLearningMaterials;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("courseLearningMaterials")
     */
    public $courseLearningMaterials;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("previousIndexing")
     */
    public $previousIndexing;

    /**
     * MeshDescriptorDTO constructor.
     * @param string $id
     * @param string $name
     * @param string $annotation
     * @param \DateTime $createdAt
     * @param \DateTime $updatedAt
     */
    public function __construct(
        $id,
        $name,
        $annotation,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->annotation = $annotation;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->courses= [];
        $this->objectives = [];
        $this->sessions = [];
        $this->concepts = [];
        $this->qualifiers = [];
        $this->trees = [];
        $this->sessionLearningMaterials = [];
        $this->courseLearningMaterials = [];
    }
}
