<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class CurriculumInventoryReportRepository extends EntityRepository
{
    /**
     * Retrieves a list of events (derived from published sessions/offerings and independent learning sessions)
     * in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An assoc. array of assoc. arrays, each item representing an event, keyed off by event id.
     */
    public function getEvents(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT
  s.session_id AS 'event_id',
  s.title,
  sd.description,
  stxam.method_id,
  st.assessment AS is_assessment_method,
  ao.name AS assessment_option_name,
  (IF(sbs.count_offerings_once,
    TIMESTAMPDIFF(MINUTE, os.start_date, os.end_date),
    SUM(TIMESTAMPDIFF(MINUTE, o.start_date, o.end_date))
  )) AS duration
FROM
  `session` s
  LEFT JOIN offering o ON o.session_id = s.session_id AND o.deleted = 0
  LEFT JOIN offering os ON os.offering_id = (
    SELECT offering_id FROM offering WHERE offering.session_id = s.session_id AND offering.deleted = 0
    ORDER BY TIMESTAMPDIFF(MINUTE, offering.start_date, offering.end_date) DESC LIMIT 1
  )
  LEFT JOIN session_description sd ON sd.session_id = s.session_id
  JOIN session_type st ON st.session_type_id = s.session_type_id
  LEFT JOIN session_type_x_aamc_method stxam ON stxam.session_type_id = st.session_type_id
  LEFT JOIN assessment_option ao ON ao.assessment_option_id = st.assessment_option_id
  JOIN course c ON c.course_id = s.course_id
  LEFT JOIN ilm_session_facet sf ON sf.session_id = s.session_id
  JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
  LEFT JOIN curriculum_inventory_sequence_block_session sbs ON sbs.session_id = s.session_id
WHERE
  c.deleted = 0
  AND s.deleted = 0
  AND s.publish_event_id IS NOT NULL
  AND sf.ilm_session_facet_id IS NULL
  AND sb.report_id = :report_id
GROUP BY
  s.session_id,
  s.title,
  sd.description,
  stxam.method_id,
  is_assessment_method

UNION

SELECT
  s.session_id AS 'event_id',
  s.title,
  sd.description,
  stxam.method_id,
  st.assessment AS is_assessment_method,
  ao.name AS assessment_option_name,
  FLOOR(sf.hours * 60) AS duration
FROM
  `session` s
  LEFT JOIN session_description sd ON sd.session_id = s.session_id
  JOIN session_type st ON st.session_type_id = s.session_type_id
  LEFT JOIN session_type_x_aamc_method stxam ON stxam.session_type_id = st.session_type_id
  LEFT JOIN assessment_option ao ON ao.assessment_option_id = st.assessment_option_id
  JOIN course c ON c.course_id = s.course_id
  JOIN ilm_session_facet sf ON sf.session_id = s.session_id
  JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE
  c.deleted = 0
  AND s.deleted = 0
  AND s.publish_event_id IS NOT NULL
  AND sb.report_id = :report_id
GROUP BY
  s.session_id,
  s.title,
  sd.description,
  stxam.method_id,
  is_assessment_method
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            $rhett[$row['event_id']] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves keywords (MeSH descriptors) associated with events (sessions)
     * in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An array of assoc. arrays, each sub-array representing a keyword.
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report)
    {
        $sql =<<< EOL
SELECT
  s.session_id AS 'event_id',
  md.mesh_descriptor_uid,
  md.name
FROM
  `session` s
  JOIN course c ON c.course_id = s.course_id
  JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
  JOIN session_x_mesh sxm ON sxm.session_id = s.session_id
  JOIN mesh_descriptor md ON md.mesh_descriptor_uid = sxm.mesh_descriptor_uid
WHERE
  c.deleted = 0
  AND s.deleted = 0
  AND s.publish_event_id IS NOT NULL
  AND sb.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rhett = $stmt->fetchAll();
        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves a lookup map of events ('sessions') in a given curriculum inventory report,
     * grouped and keyed off by sequence block id.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getEventReferencesForSequenceBlocks (CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<< EOL
SELECT
  sb.sequence_block_id,
  s.session_id AS 'event_id',
  !s.supplemental AS 'required'
FROM `session` s
  JOIN `course` c ON c.course_id = s.course_id
  JOIN curriculum_inventory_sequence_block sb ON sb.course_id = c.course_id
WHERE
  s.deleted = 0
  AND s.publish_event_id IS NOT NULL
  AND c.deleted = 0
  AND sb.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            if (! array_key_exists($row['sequence_block_id'], $rhett)) {
                $rhett[$row['sequence_block_id']] = [];
            }
            $rhett[$row['sequence_block_id']][] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves all program objectives in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An associative array of arrays, keyed off by objective id.
     *   Each item is an associative array, containing
     *   the objective's id and title (keys: "objective_id" and "title").
     */
    public function getProgramObjectives(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT DISTINCT
  o.objective_id,
  o.title
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
  JOIN cohort co ON co.cohort_id = cxc.cohort_id
  JOIN program_year py ON py.program_year_id = co.program_year_id
  JOIN program p2 ON p2.program_id = py.program_id AND p2.owning_school_id = p.owning_school_id
  JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
  JOIN objective o ON o.objective_id = pyxo.objective_id
WHERE
  c.deleted = 0
  AND py.deleted = 0
  AND r.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            $rhett[$row['objective_id']] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves all course objectives in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array an associative array of arrays, keyed off by objective id.
     *   Each item is an associative array, containing
     *   the objective's id and title (keys: "objective_id" and "title").
     */
    public function getCourseObjectives(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT DISTINCT
  o.objective_id,
  o.title
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN course_x_objective cxo ON cxo.course_id = c.course_id
  JOIN objective o ON o.objective_id = cxo.objective_id
WHERE
  c.deleted = 0
  AND r.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            $rhett[$row['objective_id']] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves all session objectives in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An associative array of arrays, keyed off by objective id.
     *   Each item is an associative array, containing
     *   the objective's id and title (keys: "objective_id" and "title").
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT DISTINCT
  o.objective_id,
  o.title
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN `session` s ON s.course_id = c.course_id
  JOIN session_x_objective sxo ON sxo.session_id = s.session_id
  JOIN objective o ON o.objective_id = sxo.objective_id
WHERE
  s.deleted = 0
  AND s.publish_event_id IS NOT NULL
  AND c.deleted = 0
  AND r.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            $rhett[$row['objective_id']] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }


    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getCompetencyObjectReferencesForEvents(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT DISTINCT
  s.session_id AS 'event_id',
  so.objective_id AS 'session_objective_id',
  o.objective_id as 'course_objective_id',
  o2.objective_id AS 'program_objective_id'
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN `session` s ON s.course_id = c.course_id
  JOIN session_x_objective sxo ON sxo.session_id = s.session_id
  JOIN objective so ON so.objective_id = sxo.objective_id
  LEFT JOIN objective_x_objective oxo ON oxo.objective_id = so.objective_id
  LEFT JOIN course_x_objective cxo ON cxo.objective_id = oxo.parent_objective_id
  LEFT JOIN objective o ON o.objective_id = cxo.objective_id
  LEFT JOIN objective_x_objective oxo2 ON oxo2.objective_id = o.objective_id
  LEFT JOIN objective o2 ON o2.objective_id = oxo2.parent_objective_id
WHERE
  s.deleted = 0
  AND s.publish_event_id IS NOT NULL
  AND c.deleted = 0
  AND r.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            $eventId = $row['event_id'];
            if (! array_key_exists($eventId, $rhett)) {
                $rhett[$eventId] = [
                    'session_objectives' => [],
                    'course_objectives' => [],
                    'program_objectives' => [],
                ];
            }
            if (isset($row['session_objective_id'])
                && ! in_array($row['session_objective_id'], $rhett[$eventId]['session_objectives'])) {
                $rhett[$eventId]['session_objectives'][] = $row['session_objective_id'];
            }
            if (isset($row['course_objective_id'])
                && ! in_array($row['course_objective_id'], $rhett[$eventId]['course_objectives'])) {
                $rhett[$eventId]['course_objectives'][] = $row['course_objective_id'];
            }
            if (isset($row['program_objective_id'])
                && ! in_array($row['program_objective_id'], $rhett[$eventId]['program_objectives'])) {
                $rhett[$eventId]['program_objectives'][] = $row['program_objective_id'];
            }
        }
        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getCompetencyObjectReferencesForSequenceBlocks(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT DISTINCT
  sb.sequence_block_id,
  o.objective_id as 'course_objective_id',
  o2.objective_id AS 'program_objective_id'
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN course_x_objective cxo ON cxo.course_id = c.course_id
  LEFT JOIN objective o ON o.objective_id = cxo.objective_id
  LEFT JOIN objective_x_objective oxo ON oxo.objective_id = o.objective_id
  LEFT JOIN objective o2 ON o2.objective_id = oxo.parent_objective_id
WHERE
  c.deleted = 0
  AND r.report_id = :report_id
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("report_id", $report->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach($rows as $row) {
            $sequenceBlockId = $row['sequence_block_id'];
            if (! array_key_exists($sequenceBlockId, $rhett)) {
                $rhett[$sequenceBlockId] = [
                    'course_objectives' => [],
                    'program_objectives' => [],
                ];
            }
            if (isset($row['course_objective_id'])
                && ! in_array($row['course_objective_id'], $rhett[$sequenceBlockId]['course_objectives'])) {
                $rhett[$sequenceBlockId]['course_objectives'][] = $row['course_objective_id'];
            }
            if (isset($row['program_objective_id'])
                && ! in_array($row['program_objective_id'], $rhett[$sequenceBlockId]['program_objectives'])) {
                $rhett[$sequenceBlockId]['program_objectives'][] = $row['program_objective_id'];
            }

        }
        $stmt->closeCursor();
        return $rhett;
    }
}