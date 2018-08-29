<?php
namespace AppBundle\Entity;

interface SessionStampableInterface
{
    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}
