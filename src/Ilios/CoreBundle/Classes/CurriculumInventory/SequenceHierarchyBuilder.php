<?php
namespace Ilios\CoreBundle\Classes\CurriculumInventory;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventorySequenceManagerInterface;

/**
 * Utility class for curriculum inventory management.
 *
 * Provides functionality for transforming a list of blocks
 * within a sequence into a nested hierarchy.
 *
 * @package Ilios\CoreBundle\Classes\CurriculumInventory
 */
class SequenceBlockHierarchyBuilder
{
    /**
     * Recursively builds a hierarchy of nested sequence blocks, based on their parent/child relationships
     * @param array $sequenceBlocks A flat array of sequence blocks.
     * @param int|null $parentBlockId The id of the parent sequence block, NULL if for top-level blocks.
     * @return array The nested sequence blocks.
     */
    public function buildSequenceBlockHierarchy (array $sequenceBlocks, $parentBlockId = null)
    {

        $rhett = array();
        $remainder = array();

        for ($i = 0, $n = count($sequenceBlocks); $i < $n; $i++) {
            if ($parentBlockId === $sequenceBlocks[$i]['parent_sequence_block_id']) {
                $rhett[] = $sequenceBlocks[$i];
            } else {
                $remainder[] = $sequenceBlocks[$i];
            }
        }
        for ($i = 0, $n = count($rhett); $i < $n; $i++) {
            // recursion!
            $children = $this->buildSequenceBlockHierarchy($remainder, $rhett[$i]['sequence_block_id']);
            if (count($children)) {
                // sort children if the sort order demands it
                if (CurriculumInventorySequenceManagerInterface::ORDERED == $rhett[$i]['child_sequence_order']) {
                    usort($children, 'Curriculum_Inventory_Sequence_Block::orderedSortSequenceBlocks');
                } else {
                    usort($children, 'Curriculum_Inventory_Sequence_Block::defaultSortSequenceBlocks');
                }
                $rhett[$i]['children'] = $children;
            }
        }
        if (is_null($parentBlockId)) {
            usort($rhett, 'Curriculum_Inventory_Sequence_Block::defaultSortSequenceBlocks');
        }
        return $rhett;
    }

    /**
     * Comparison function for sorting sequence block arrays.
     * The applied criterion for comparison is the "order_in_sequence" property.
     * @param array $a associative array representing a sequence block
     * @param array $b associative array representing a sequence block
     * @return int
     * @see usort()
     * @see Curriculum_Inventory_Sequence_Block::buildSequenceBlockHierarchy()
     */
    public static function orderedSortSequenceBlocks (array $a, array $b)
    {
        if ($a['order_in_sequence'] === $b['order_in_sequence']) {
            return 0;
        }
        return ($a['order_in_sequence'] > $b['order_in_sequence']) ? 1 : -1;
    }

    /**
     * Comparison function for sorting sequence block arrays.
     * The applied, ranked criteria for comparison are:
     * 1. "academic level id"
     *      Ideally, this would be 'level' value of the actual corresponding "academic level" record.
     *      However, this data point is not available at this point of data processing.
     *      Still, we can infer the level from the sequentially generated level ids.
     *      Hokey, but works [ST 2013/08/14]
     *      Numeric sort, ascending.
     * 2. "start date"
     *      Numeric sort on timestamps, ascending. NULL values will be treated as unix timestamp 0.
     * 3. "title"
     *    Alphabetical sort.
     * 4. "sequence block id"
     *    A last resort. Numeric sort, ascending.
     *
     * @param array $a associative array representing a sequence block
     * @param array $b associative array representing a sequence block
     * @return int
     * @see usort()
     * @see Curriculum_Inventory_Sequence_Block::buildSequenceBlockHierarchy()
     */
    public static function defaultSortSequenceBlocks (array $a, array $b)
    {
        // 1. academic level id
        if ($a['academic_level_id'] > $b['academic_level_id']) {
            return 1;
        } elseif ($a['academic_level_id'] < $b['academic_level_id']) {
            return -1;
        }

        // 2. start date
        $startDateA = $a['start_date'] ? strtotime($a['start_date']) : 0;
        $startDateB = $b['start_date'] ? strtotime($b['start_date']) : 0;

        if ($startDateA > $startDateB) {
            return 1;
        } elseif ($startDateA < $startDateB) {
            return -1;
        }

        // 3. title comparison
        $n = strcasecmp($a['title'], $b['title']);
        if ($n) {
            return $n;
        }

        // 4. sequence block id comparison
        return ($a['sequence_block_id'] > $b['sequence_block_id']) ? 1 : -1;
    }
}