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
    protected $id;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $annotation;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("createdAt")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("updatedAt")
     */
    protected $updatedAt;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $objectives;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

    /**
     * @var string[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $concepts;

    /**
     * @var string[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $qualifiers;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $trees;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionLearningMaterials")
     */
    protected $sessionLearningMaterials;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("courseLearningMaterials")
     */
    protected $courseLearningMaterials;

    /**
     * @var int
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("previousIndexing")
     */
    protected $previousIndexing;

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
        $this->title = $name;
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
