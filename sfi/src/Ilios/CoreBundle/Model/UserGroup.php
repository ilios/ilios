<?php

namespace Ilios\CoreBundle\Model;

/**
 * Class UserGroup
 * @package Ilios\CoreBundle\Model
 */
class UserGroup implements UserGroupInterface
{
    const STUDENT = 'student';
    const INSTRUCTOR = 'instructor';

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var GroupInterface
     */
    protected $group;

    /**
     * @var string
     */
    protected $type;

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::STUDENT,
            self::INSTRUCTOR
        ];
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param GroupInterface $group
     */
    public function setGroup(GroupInterface $group)
    {
        $this->group = $group;
    }

    /**
     * @return GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        if (!in_array($type, static::getTypes())) {
            throw new \InvalidARgumentException('Invalid type for User - Group relationship.');
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
