<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface PendingUserUpdateInterface
 */
interface PendingUserUpdateInterface extends
    IdentifiableEntityInterface
{
    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * Set property
     *
     * @param string $property
     */
    public function setProperty($property);

    /**
     * Get property
     *
     * @return string
     */
    public function getProperty();

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}
