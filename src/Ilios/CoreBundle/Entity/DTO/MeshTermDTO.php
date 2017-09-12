<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class MeshTermDTO
 *
 * @IS\DTO
 */
class MeshTermDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $meshTermUid;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $lexicalTag;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $conceptPreferred;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $recordPreferred;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $permuted;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     * @deprecated
     */
    public $printable;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $createdAt;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $updatedAt;

    /**
     * @var integer[]
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $concepts;

    /**
     * MeshTermDTO constructor.
     * @param $id
     * @param $meshTermUid
     * @param $name
     * @param $lexicalTag
     * @param $conceptPrefered
     * @param $recordPreferred
     * @param $permuted
     * @param $printable
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct(
        $id,
        $meshTermUid,
        $name,
        $lexicalTag,
        $conceptPreferred,
        $recordPreferred,
        $permuted,
        $printable,
        $createdAt,
        $updatedAt
    ) {
        $this->id = $id;
        $this->meshTermUid = $meshTermUid;
        $this->name = $name;
        $this->lexicalTag = $lexicalTag;
        $this->conceptPreferred = $conceptPreferred;
        $this->recordPreferred = $recordPreferred;
        $this->permuted = $permuted;
        $this->printable = $printable;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        $this->concepts = [];
    }
}
