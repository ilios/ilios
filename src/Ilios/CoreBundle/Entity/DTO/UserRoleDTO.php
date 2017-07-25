<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class UserRoleDTO
 * @package Ilios\CoreBundle\Entity\DTO
 *
 * @IS\DTO
 */
class UserRoleDTO
{
    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * UserRoleDTO constructor.
     * @param $id
     * @param $title
     */
    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
