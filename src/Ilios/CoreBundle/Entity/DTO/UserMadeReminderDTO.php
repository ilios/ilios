<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class UserMadeReminderDTO
 *
 * @IS\DTO
 */
class UserMadeReminderDTO
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
    public $note;

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
    public $dueDate;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $closed;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $user;

    /**
     * UserMadeReminderDTO constructor.
     * @param $id
     * @param $note
     * @param $createdAt
     * @param $dueDate
     */
    public function __construct(
        $id,
        $note,
        $createdAt,
        $dueDate,
        $closed
    ) {
        $this->id = $id;
        $this->note = $note;
        $this->createdAt = $createdAt;
        $this->dueDate = $dueDate;
        $this->closed = $closed;
    }
}
