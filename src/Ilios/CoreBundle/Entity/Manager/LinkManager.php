<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterials\LinkInterface;

/**
 * Class LinkManager
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
class LinkManager implements LinkManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

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
        return $this->repository->findOneBy($criteria, $orderBy);
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
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
