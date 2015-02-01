<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class IngestionException
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ingestion_exception")
 *
 * @JMS\ExclusionPolicy("all")
 */
class IngestionException implements IngestionExceptionInterface
{
    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var string
     *
     * @ORM\Column(name="ingested_wide_uid", type="string", length=32)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $uuid;

    /**
     * Used as primary key.
     * @var UserInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $user;

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->ingestedWideUid = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->ingestedWideUid : $this->uuid;
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
}
