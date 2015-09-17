<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
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
        foreach ($rows as $row) {
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
        foreach ($rows as $row) {
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
        foreach ($rows as $row) {
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
        foreach ($rows as $row) {
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
        foreach ($rows as $row) {
            $rhett[$row['objective_id']] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }


    /**
     * Retrieves all the competency object references per event in a given report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An associative array of arrays, keyed off by event id.
     *     Each sub-array is in turn a two item map, containing a list of course objectives ids
     *     under 'course_objectives', a list of program objective ids under 'program_objective'
     *     and a list of session objective ids under under 'session_objective_ids'.
     *
     *   <pre>
     *   [ <sequence block id> => [
     *       "course_objectives" => [ <list of course objectives ids> ]
     *       "program_objectives" => [ <list of program objective ids> ]
     *       "session_objectives" => [ <list of session objective ids> ]
     *     ],
     *     ...
     *   ],
     *   </pre>
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
        foreach ($rows as $row) {
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
     * Retrieves all the competency object references per sequence block in a given report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An associative array of arrays, keyed off by sequence block id.
     *     Each sub-array is in turn a two item map, containing a list of course objectives ids
     *     under 'course_objectives' and a list of program objective ids under 'program_objective'.
     *
     *   <pre>
     *   [ <sequence block id> => [
     *       "course_objectives" => [ <list of course objectives ids> ]
     *       "program_objectives" => [ <list of program objective ids> ]
     *     ],
     *     ...
     *   ],
     *   </pre>
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
        foreach ($rows as $row) {
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

    /**
     * Retrieves the relations between given program-objectives and PCRS (via competencies).
     * @param array $programObjectivesId
     * @param array $pcrsIds
     * @return array
     */
    public function getProgramObjectivesToPcrsRelations (array $programObjectivesId, array $pcrsIds)
    {
        $rhett = [
            'relations' => [],
            'program_objective_ids' => [],
            'pcrs_ids' => [],
        ];

        if (! count($programObjectivesId) || ! count($pcrsIds)) {
            return $rhett;
        }

        $sql =<<<EOL
SELECT DISTINCT
  o.objective_id,
  cxam.pcrs_id
FROM
  objective o
  JOIN competency c ON o.competency_id = c.competency_id
  JOIN competency_x_aamc_pcrs cxam ON c.competency_id = cxam.competency_id
  JOIN aamc_pcrs am ON am.pcrs_id = cxam.pcrs_id
WHERE
  am.pcrs_id IN (:pcrs_ids)
  AND o.objective_id IN (:program_objective_ids)
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("pcrs_ids", $pcrsIds, Connection::PARAM_STR_ARRAY);
        $stmt->bindValue("program_objective_ids", $programObjectivesId, Connection::PARAM_INT_ARRAY);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $rhett['relations'][] = [
                'rel1' => $row['objective_id'],
                'rel2' => $row['pcrs_id'],
            ];
            $rhett['program_objective_ids'][] = $row['objective_id'];
            $rhett['pcrs_ids'][] = $row['pcrs_id'];
        }

        // dedupe
        $rhett['program_objective_ids'] = array_values(array_unique($rhett['program_objective_ids']));
        $rhett['pcrs_ids'] = array_values(array_unique($rhett['pcrs_ids']));

        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves the relations between given course- and program-objectives.
     * @param array $courseObjectiveIds
     * @param array $programObjectiveIds
     * @return array
     */
    public function getCourseObjectivesToProgramObjectivesRelations (
        array $courseObjectiveIds,
        array $programObjectiveIds
    )
    {
        $rhett = [
            'relations' => [],
            'course_objective_ids' => [],
            'program_objective_ids' => [],
        ];

        if (! count($courseObjectiveIds) || ! count($programObjectiveIds)) {
            return $rhett;
        }

        $sql =<<<EOL
SELECT DISTINCT
   oxo.objective_id,
   oxo.parent_objective_id
FROM
   objective_x_objective oxo
   JOIN course_x_objective cxo ON cxo.objective_id = oxo.objective_id
   JOIN program_year_x_objective pyxo ON pyxo.objective_id = oxo.parent_objective_id
WHERE
   oxo.objective_id IN (:course_objective_ids)
   AND oxo.parent_objective_id IN (:program_objective_ids)
EOL;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("course_objective_ids", $courseObjectiveIds, Connection::PARAM_INT_ARRAY);
        $stmt->bindValue("program_objective_ids", $programObjectiveIds, Connection::PARAM_INT_ARRAY);
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach ($rows as $row) {
            $rhett['relations'][] = [
                'rel1' => $row['parent_objective_id'],
                'rel2' => $row['objective_id'],
            ];
            $rhett['course_objective_ids'][] = $row['objective_id'];
            $rhett['program_objective_ids'][] = $row['parent_objective_id'];
        }

        // dedupe
        $rhett['course_objective_ids'] = array_values(array_unique($rhett['course_objective_ids']));
        $rhett['program_objective_ids'] = array_values(array_unique($rhett['program_objective_ids']));

        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves the relations between given session- and course-objectives.
     *
     * @param array $sessionObjectiveIds
     * @param array $courseObjectiveIds
     * @return array
     */
    public function getSessionObjectivesToCourseObjectivesRelations (
        array $sessionObjectiveIds,
        array $courseObjectiveIds
    )
    {
        $rhett = [
            'relations' => [],
            'session_objective_ids' => [],
            'course_objective_ids' => [],
        ];

        if (! count($sessionObjectiveIds) || ! count($courseObjectiveIds)) {
            return $rhett;
        }

        $sql =<<<EOL
SELECT DISTINCT
  oxo.objective_id,
  oxo.parent_objective_id
FROM
  objective_x_objective oxo
  JOIN session_x_objective sxo ON sxo.objective_id = oxo.objective_id
  JOIN course_x_objective cxo ON cxo.objective_id = oxo.parent_objective_id
WHERE
  oxo.objective_id IN (:session_objective_ids)
  AND oxo.parent_objective_id IN (:course_objective_ids)
EOL;

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("session_objective_ids", $sessionObjectiveIds, Connection::PARAM_INT_ARRAY);
        $stmt->bindValue("course_objective_ids", $courseObjectiveIds, Connection::PARAM_INT_ARRAY);
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        foreach ($rows as $row) {
            $rhett['relations'][] = [
                'rel1' => $row['parent_objective_id'],
                'rel2' => $row['objective_id'],
            ];
            $rhett['session_objective_ids'][] = $row['objective_id'];
            $rhett['course_objective_ids'][] = $row['parent_objective_id'];
        }
        
        // dedupe
        $rhett['session_objective_ids'] = array_values(array_unique($rhett['session_objective_ids']));
        $rhett['course_objective_ids'] = array_values(array_unique($rhett['course_objective_ids']));

        $stmt->closeCursor();
        return $rhett;
    }

    /**
     * Retrieves all PCRS linked to sequence blocks (via objectives and competencies) in a given inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array A nested array of associative arrays, keyed off by 'pcrs_id'. Each sub-array represents a PCRS
     *    and is itself an associative array with values being keyed off by 'pcrs_id' and 'description'.
     */
    public function getPcrs(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $sql =<<<EOL
SELECT
  am.pcrs_id,
  am.description
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
  JOIN cohort co ON co.cohort_id = cxc.cohort_id
  JOIN program_year py ON py.program_year_id = co.program_year_id
  JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
  JOIN objective o ON o.objective_id = pyxo.objective_id
  JOIN competency cm ON cm.competency_id = o.competency_id AND cm.owning_school_id = p.owning_school_id
  JOIN competency cm2 ON cm2.competency_id = cm.parent_competency_id
  JOIN competency_x_aamc_pcrs cxm ON cxm.competency_id = cm2.competency_id
  JOIN aamc_pcrs am ON am.pcrs_id = cxm.pcrs_id
WHERE
  c.deleted = 0
  AND py.deleted = 0
  AND r.report_id = :report_id

UNION

SELECT
  am.pcrs_id,
  am.description
FROM
  curriculum_inventory_report r
  JOIN program p ON p.program_id = r.program_id
  JOIN curriculum_inventory_sequence_block sb ON sb.report_id = r.report_id
  JOIN course c ON c.course_id = sb.course_id
  JOIN course_x_cohort cxc ON cxc.course_id = c.course_id
  JOIN cohort co ON co.cohort_id = cxc.cohort_id
  JOIN program_year py ON py.program_year_id = co.program_year_id
  JOIN program_year_x_objective pyxo ON pyxo.program_year_id = py.program_year_id
  JOIN objective o ON o.objective_id = pyxo.objective_id
  JOIN competency cm ON cm.competency_id = o.competency_id AND cm.owning_school_id = p.owning_school_id
  JOIN competency_x_aamc_pcrs cxm ON cxm.competency_id = cm.competency_id
  JOIN aamc_pcrs am ON am.pcrs_id = cxm.pcrs_id
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
        foreach ($rows as $row) {
            $rhett[$row['pcrs_id']] = $row;
        }
        $stmt->closeCursor();
        return $rhett;
    }
}