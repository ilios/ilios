<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SessionDTO
 * Data transfer object for a session.
 * @package Ilios\CoreBundle\Entity\DTO

 */
class SessionDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $title;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     * @JMS\SerializedName("attireRequired")
     */
    public $attireRequired;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     * @JMS\SerializedName("equipmentRequired")
     */
    public $equipmentRequired;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $supplemental;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     * @JMS\Type("boolean")
     */
    public $published;

    /**
     * @var \DateTime
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("updatedAt")
     */
    public $updatedAt;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("sessionType")
     */
    public $sessionType;

    /**
     * @var int
     * @JMS\Type("string")
     */
    public $course;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("ilmSession")
     */
    public $ilmSession;


    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("meshDescriptors")
     */
    public $meshDescriptors;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("sessionDescription")
     */
    public $sessionDescription;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learningMaterials")
     */
    public $learningMaterials;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $offerings;

    public function __construct(
        $id,
        $title,
        $attireRequired,
        $equipmentRequired,
        $supplemental,
        $publishedAsTbd,
        $published,
        $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->attireRequired = $attireRequired;
        $this->equipmentRequired = $equipmentRequired;
        $this->supplemental = $supplemental;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;
        $this->updatedAt = $updatedAt;

        $this->terms = [];
        $this->objectives = [];
        $this->meshDescriptors = [];
        $this->learningMaterials = [];
        $this->offerings = [];
    }
}
