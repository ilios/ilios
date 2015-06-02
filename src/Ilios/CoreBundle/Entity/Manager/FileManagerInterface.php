<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\LearningMaterials\FileInterface;

/**
 * Interface FileManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
interface FileManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return FileInterface
     */
    public function findFileBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|FileInterface[]
     */
    public function findFilesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param FileInterface $file
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateFile(
        FileInterface $file,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param FileInterface $file
     *
     * @return void
     */
    public function deleteFile(
        FileInterface $file
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return FileInterface
     */
    public function createFile();
}
