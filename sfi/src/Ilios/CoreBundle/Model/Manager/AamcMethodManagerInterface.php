<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AamcMethodInterface;

/**
 * Interface AamcMethodManagerInterface
 */
interface AamcMethodManagerInterface
{
    /** 
     *@return AamcMethodInterface
     */
    public function createAamcMethod();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AamcMethodInterface
     */
    public function findAamcMethodBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return AamcMethodInterface[]|Collection
     */
    public function findAamcMethodsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAamcMethod(AamcMethodInterface $aamcMethod, $andFlush = true);

    /**
     * @param AamcMethodInterface $aamcMethod
     *
     * @return void
     */
    public function deleteAamcMethod(AamcMethodInterface $aamcMethod);

    /**
     * @return string
     */
    public function getClass();
}
