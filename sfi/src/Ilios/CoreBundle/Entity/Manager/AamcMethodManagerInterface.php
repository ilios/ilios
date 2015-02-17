<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Interface AamcMethodManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface AamcMethodManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AamcMethodInterface
     */
    public function findAamcMethodBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AamcMethodInterface[]|Collection
     */
    public function findAamcMethodsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAamcMethod(
        AamcMethodInterface $aamcMethod,
        $andFlush = true
    );

    /**
     * @param AamcMethodInterface $aamcMethod
     *
     * @return void
     */
    public function deleteAamcMethod(
        AamcMethodInterface $aamcMethod
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AamcMethodInterface
     */
    public function createAamcMethod();
}
