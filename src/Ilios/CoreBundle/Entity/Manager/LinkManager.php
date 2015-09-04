<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterials\LinkInterface;

/**
 * Class LinkManager
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
class LinkManager extends AbstractManager implements LinkManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LinkInterface
     */
    public function findLinkBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|LinkInterface[]
     */
    public function findLinksBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LinkInterface $link
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateLink(
        LinkInterface $link,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($link);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($link));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LinkInterface $link
     */
    public function deleteLink(
        LinkInterface $link
    ) {
        $this->em->remove($link);
        $this->em->flush();
    }

    /**
     * @return LinkInterface
     */
    public function createLink()
    {
        $class = $this->getClass();
        return new $class();
    }
}
