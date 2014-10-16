<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface UserGroupInterface
 * @package Ilios\CoreBundle\Model
 */
interface UserGroupInterface
{
    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param GroupInterface $group
     */
    public function setGroup(GroupInterface $group);

    /**
     * @return GroupInterface
     */
    public function getGroup();

    /**
     * @param $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();
}
