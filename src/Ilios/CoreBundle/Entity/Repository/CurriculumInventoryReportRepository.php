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
        return $rhett;
    }

    /**
     * Retrieves keywords (MeSH descriptors) associated with events (sessions)
     * in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return array An array of assoc. arrays, each sub-array representing a keyword.
     */
    public function getEventKeywords (CurriculumInventoryReportInterface $report)
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
        return $stmt->fetchAll();
    }
}