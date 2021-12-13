<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;

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

    public function getType(): string;

    /**
     * Set property
     *
     * @param string $property
     */
    public function setProperty($property);

    /**
     * Get property
     */
    public function getProperty(): string;

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value);

    /**
     * Get value
     */
    public function getValue(): string;

    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;
}
