<?php
namespace Ilios\CoreBundle\Classes\CurriculumInventory;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Utility class for curriculum inventory management.
 *
 * Provides functionality for sorting a hierarchy of nested sequence-blocks.
 *
 * @package Ilios\CoreBundle\Classes\CurriculumInventory
 */
class SequenceBlockHierarchySorter
{
    /**
     * Recursively sorts a hierarchy of nested sequence blocks.
     *
     * @param ArrayCollection|CurriculumInventorySequenceBlockInterface[] $collection A collection of sequence blocks.
     * @param int $sortOrder The type of sort order strategy to apply.
     *
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function sort(
        ArrayCollection $collection,
        $sortOrder = CurriculumInventorySequenceBlockInterface::UNORDERED
    ) {
        // 1. recursively sort any children of each sequence block in the given list.
        $iterator = $collection->getIterator();
        /** @var CurriculumInventorySequenceBlockInterface $element */
        foreach ($iterator as $element) {
            $children = $element->getChildren();
            if (!$children->isEmpty()) {
                $children = $this->sort($children, $element->getChildSequenceOrder());
                $element->setChildren($children);
            }
        }

        // 2. sort the given list of sequence blocks.
        $iterator = $collection->getIterator();

        switch ($sortOrder) {
            case CurriculumInventorySequenceBlockInterface::ORDERED:
                $iterator->uasort(array(__CLASS__, 'compareSequenceBlocksWithOrderedStrategy'));
                break;
            case CurriculumInventorySequenceBlockInterface::PARALLEL:
            case CurriculumInventorySequenceBlockInterface::UNORDERED:
            default:
                $iterator->uasort(array(__CLASS__, 'compareSequenceBlocksWithDefaultStrategy'));
                break;
        }

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * Callback function for comparing sequence blocks.
     * The applied criterion for comparison is the </pre>"orderInSequence</pre> property.
     *
     * @param CurriculumInventorySequenceBlockInterface $a
     * @param CurriculumInventorySequenceBlockInterface $b
     * @return int One of -1, 0, 1.
     */
    public static function compareSequenceBlocksWithOrderedStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b
    ) {
        if ($a->getOrderInSequence() === $b->getOrderInSequence()) {
            return 0;
        }
        return ($a->getOrderInSequence() > $b->getOrderInSequence()) ? 1 : -1;
    }

    /**
     * Callback function for comparing sequence blocks.
     * The applied, ranked criteria for comparison are:
     * 1. "academic level"
     *      Numeric sort, ascending.
     * 2. "start date"
     *      Numeric sort on timestamps, ascending. NULL values will be treated as unix timestamp 0.
     * 3. "title"
     *    Alphabetical sort.
     * 4. "sequence block id"
     *    A last resort. Numeric sort, ascending.
     *
     * @param CurriculumInventorySequenceBlockInterface $a
     * @param CurriculumInventorySequenceBlockInterface $b
     * @return int One of -1, 0, 1.
     * @see usort()
     * @see Curriculum_Inventory_Sequence_Block::buildSequenceBlockHierarchy()
     */
    public static function compareSequenceBlocksWithDefaultStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b
    ) {
        // 1. academic level id
        if ($a->getAcademicLevel()->getLevel() > $b->getAcademicLevel()->getLevel()) {
            return 1;
        } elseif ($a->getAcademicLevel()->getLevel() < $b->getAcademicLevel()->getLevel()) {
            return -1;
        }

        // 2. start date
        $startDateA = $a->getStartDate() ? $a->getStartDate()->getTimestamp() : 0;
        $startDateB = $b->getStartDate() ? $b->getStartDate()->getTimestamp() : 0;

        if ($startDateA > $startDateB) {
            return 1;
        } elseif ($startDateA < $startDateB) {
            return -1;
        }

        // 3. title comparison
        $n = strcasecmp($a->getTitle(), $b->getTitle());
        if ($n) {
            return $n > 0 ? 1 : -1;
        }

        // 4. sequence block id comparison
        if ($a->getId() > $b->getId()) {
            return 1;
        } elseif ($a->getId() < $b->getId()) {
            return -1;
        }
        return 0;
    }
}
