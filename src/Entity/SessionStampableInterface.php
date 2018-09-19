<?php
namespace App\Entity;

interface SessionStampableInterface
{
    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}
