<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class SessionDTO
 * Data transfer object for a session.
 * @package Ilios\CoreBundle\Entity\DTO
 *
 * @IS\DTO
 */
class SessionDTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var boolean
     */
    public $attireRequired;

    /**
     * @var boolean
     */
    public $equipmentRequired;

    /**
     * @var boolean
     */
    public $supplemental;

    /**
     * @var boolean
     */
    public $publishedAsTbd;

    /**
     * @var boolean
     */
    public $published;

    /**
     * @var \DateTime
     */
    public $updatedAt;

    /**
     * @var int
     */
    public $sessionType;

    /**
     * @var int
     */
    public $course;

    /**
     * @var int
     */
    public $ilmSession;

    /**
     * @var int[]
     */
    public $terms;

    /**
     * @var int[]
     */
    public $objectives;

    /**
     * @var int[]
     */
    public $meshDescriptors;

    /**
     * @var int
     */
    public $sessionDescription;

    /**
     * @var int[]
     */
    public $learningMaterials;

    /**
     * @var int[]
     */
    public $administrators;

    /**
     * @var int[]
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
        $this->administrators = [];
    }
}
