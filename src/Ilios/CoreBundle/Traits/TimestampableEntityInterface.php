<?php

namespace Ilios\CoreBundle\Traits;

/**
 * Interface TimestampableEntityInterface
 */
interface TimestampableEntityInterface
{
    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime
     */
    public function stampUpdate();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return string
     */
    public function getClassName();
}
