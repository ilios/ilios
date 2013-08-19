<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_sequence_block" table.
 */
class Curriculum_Inventory_Sequence_Block extends Ilios_Base_Model
{
    /**
     * @var int
     */
    const REQUIRED = 1;
    /**
     * @var int
     */
    const OPTIONAL = 2;
    /**
     * @var int
     */
    const REQUIRED_IN_TRACK = 3;

    /**
     * @var int
     */
    const ORDERED = 1;

    /**
     * @var int
     */
    const UNORDERED = 2;

    /**
     * @var int
     */
    const PARALLEL = 3;

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_sequence_block', array('sequence_block_id'));
    }


    /**
     * Retrieves a sequence block by its given id.
     * @param int $sequenceBlockId The sequence block id.
     * @return array|boolean An associative array representing the sequence block record, or FALSE if none was found.
     */
    public function get ($sequenceBlockId)
    {
        $rhett = false;
        $query = $this->db->get_where($this->databaseTableName, array('sequence_block_id' => $sequenceBlockId));
        if (0 < $query->num_rows()) {
            $rhett = $query->result_array();
        }
        $query->free_result();
        return $rhett[0];
    }

    /**
     * Retrieves the sequence blocks associated with a given report.
     * @param int $reportId the report id
     * @return array a nested array of arrays, each sub-array is representing a sequence block record
     */
    public function getBlocks ($reportId)
    {
        $rhett = array();
        $clean = array();
        $clean['report_id'] = (int) $reportId;
        $sql =<<< EOL
SELECT sb.*, al.level AS 'academic_level_number',
c.clerkship_type_id AS 'course_clerkship_type_id'
FROM {$this->databaseTableName} sb
JOIN curriculum_inventory_academic_level al ON al.academic_level_id = sb.academic_level_id
LEFT JOIN course c ON c.course_id = sb.course_id
WHERE sb.report_id = {$clean['report_id']}
EOL;

        $query = $this->db->query($sql);
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Retrieves sequence blocks that are children of a given parent sequence block.
     *
     * @param int $parentSequenceBlockId The parent block's id.
     * @return array An array of associative arrays. Each item is representing a full sequence block record.
     */
    public function getChildren($parentSequenceBlockId)
    {
        $rhett = array();
        $query = $this->db->get_where($this->databaseTableName, array('parent_sequence_block_id' => $parentSequenceBlockId));
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }

    /**
     * Creates a new sequence block.
     * Note: Input validation according to business rules, like type- and range-checking, is assumed to happen further
     * upstream. In other words, this function expects validated input.
     *
     * @param int $reportId
     * @param int|null $parentBlockId
     * @param string $title
     * @param string $description
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $duration
     * @param int $academicLevelId
     * @param int $required
     * @param int $maximum
     * @param int $minimum
     * @param boolean $track
     * @param int|null $courseId
     * @param int $childSequenceOrder
     * @param int $orderInSequence
     * @return int|boolean the new sequence block id on success, or FALSE on failure
     */
    public function create ($reportId, $parentBlockId, $title, $description, $startDate, $endDate, $duration,
        $academicLevelId, $required, $maximum, $minimum, $track, $courseId, $childSequenceOrder, $orderInSequence)
    {
        $data = array();
        $data['parent_sequence_block_id'] = $parentBlockId;
        $data['report_id'] = $reportId;
        $data['title'] = $title;
        $data['description'] = $description;
        $data['required'] = $required;
        $data['maximum'] = $maximum;
        $data['minimum'] = $minimum;
        $data['track'] = $track;
        $data['course_id'] = $courseId;
        $data['academic_level_id'] = $academicLevelId;
        $data['child_sequence_order'] = $childSequenceOrder;
        $data['order_in_sequence'] = $orderInSequence;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['duration'] = $duration;

        if ($this->db->insert($this->databaseTableName, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Updates a sequence block with given data.
     * Note: Input validation according to business rules, like type- and range-checking, is assumed to happen further
     * upstream. In other words, this function expects validated input.
     *
     * @param int $sequenceBlockId
     * @param array $data An associative array containing the updated values keyed off by column name.
     *    May contain values entries for the following keys:
     *   "title", "description", "start_date", "end_date", "duration", "academic_level_id", "required", "maximum",
     *   "minimum", "track", "course_id", "child_sequence_number" and "order_in_sequence".
     */
    public function update ($sequenceBlockId, array $data)
    {
        $this->db->where('sequence_block_id', $sequenceBlockId);
        $this->db->update($this->databaseTableName, $data);

    }


    /**
     * Deletes a given sequence block and all its children.
     * @param int $sequenceBlockId The sequence block id.
     */
    public function delete ($sequenceBlockId)
    {
        $this->db->delete($this->databaseTableName, array('sequence_block_id' => $sequenceBlockId));
    }

    /**
     * Retrieves the number of sequence blocks that are children of a given sequence block.
     * @param int $sequenceBlockId The sequence block id.
     * @return int The number of child sequence blocks.
     */
    public function getNumberOfChildren ($sequenceBlockId)
    {
        $this->db->where('parent_sequence_block_id', $sequenceBlockId);
        $this->db->from($this->databaseTableName);
        return (int) $this->db->count_all_results();
    }

    /**
     * Increment the order-in-sequence value by one for all sequence blocks that
     * are direct descendants of a given sequence block, and that currently have
     * a order-in-sequence value higher than or equal to a given threshold value.
     * This may become necessary when blocks are added to/moved within an ordered sequence.
     *
     * @param int $lowerBoundary The threshold value.
     * @param int $parentSequenceBlockId The parent sequence block id.
     */
    public function incrementOrderInSequence ($lowerBoundary, $parentSequenceBlockId)
    {
        $clean = array();
        $clean['parent_sequence_block_id'] = (int) $parentSequenceBlockId;
        $clean['boundary'] = (int) $lowerBoundary;
        $sql =<<<EOL
UPDATE {$this->databaseTableName}
SET order_in_sequence = order_in_sequence + 1
WHERE order_in_sequence >= {$clean['boundary']}
AND parent_sequence_block_id = {$clean['parent_sequence_block_id']}
EOL;
        $this->db->query($sql);
    }

    /**
     * Decrement the order-in-sequence value by one for all sequence blocks that
     * are direct descendants of a given sequence block, and that currently have
     * a order-in-sequence value higher than a given threshold value.
     * This may become necessary when blocks are removed from/moved within an ordered sequence.
     *
     * @param int $lowerBoundary The threshold value.
     * @param int $parentSequenceBlockId The parent sequence block id.
     */
    public function decrementOrderInSequence ($lowerBoundary, $parentSequenceBlockId)
    {
        $clean = array();
        $clean['parent_sequence_block_id'] = (int) $parentSequenceBlockId;
        $clean['boundary'] = (int) $lowerBoundary;
        $sql =<<<EOL
UPDATE {$this->databaseTableName}
SET order_in_sequence = order_in_sequence - 1
WHERE order_in_sequence > {$clean['boundary']}
AND parent_sequence_block_id = {$clean['parent_sequence_block_id']}
EOL;
        $this->db->query($sql);
    }

    /**
     * Sets the order-in-sequence to '0' for all sequence blocks that are
     * direct descendants of a given sequence block.
     * This becomes necessary if a sequence block's child-sequence order changes
     * from "ordered" to "unordered" or "parallel".
     *
     * @param int $parentSequenceBlockId The parent sequence block id.
     */
    public function setOrderToZeroInSequence ($parentSequenceBlockId)
    {
        $this->db->update($this->databaseTableName, array('order_in_sequence' => '0'),
            array('parent_sequence_block_id' => $parentSequenceBlockId));
    }


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
                if (Curriculum_Inventory_Sequence_Block::ORDERED == $rhett[$i]['child_sequence_order']) {
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
