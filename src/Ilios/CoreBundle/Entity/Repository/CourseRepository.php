<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

class CourseRepository extends EntityRepository
{
    /**
     * Custom findBy so we can filter by related entities
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('DISTINCT c')->from('IliosCoreBundle:Course', 'c');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('c.' . $sort, $order);
            }
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('c.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->join('c.topics', 't_topic');
            $qb->andWhere($qb->expr()->in('t_topic.id', ':topics'));
            $qb->setParameter(':topics', $ids);
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('c.cohorts', 'p_cohort');
            $qb->join('p_cohort.programYear', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('programYears', $criteria)) {
            $ids = is_array($criteria['programYears']) ? $criteria['programYears'] : [$criteria['programYears']];
            $qb->join('c.cohorts', 'py_cohort');
            $qb->join('py_cohort.programYear', 'py_programYear');
            $qb->andWhere($qb->expr()->in('py_programYear.id', ':programYears'));
            $qb->setParameter(':programYears', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('c.sessions', 'i_session');
            $qb->leftJoin('i_session.offerings', 'i_offering');
            $qb->leftJoin('i_offering.instructors', 'i_user');
            $qb->leftJoin('i_offering.instructorGroups', 'i_insGroup');
            $qb->leftJoin('i_insGroup.users', 'i_groupUser');
            $qb->leftJoin('i_session.ilmSession', 'i_ilmSession');
            $qb->leftJoin('i_ilmSession.instructors', 'i_ilmInstructor');
            $qb->leftJoin('i_ilmSession.instructorGroups', 'i_ilmInsGroup');
            $qb->leftJoin('i_ilmInsGroup.users', 'i_ilmInsGroupUser');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('i_user.id', ':users'),
                $qb->expr()->in('i_groupUser.id', ':users'),
                $qb->expr()->in('i_ilmInstructor.id', ':users'),
                $qb->expr()->in('i_ilmInsGroupUser.id', ':users')
            ));
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups']) ?
                $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('c.sessions', 'ig_session');
            $qb->leftJoin('ig_session.offerings', 'ig_offering');
            $qb->leftJoin('ig_offering.instructorGroups', 'ig_igroup');
            $qb->leftJoin('ig_session.ilmSession', 'ig_ilmSession');
            $qb->leftJoin('ig_ilmSession.instructorGroups', 'ig_ilmInsGroup');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('ig_igroup.id', ':igroups'),
                    $qb->expr()->in('ig_ilmInsGroup.id', ':igroups')
                )
            );
            $qb->setParameter(':igroups', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ?
                $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('c.learningMaterials', 'lm_clm');
            $qb->leftJoin('lm_clm.learningMaterial', 'lm_lm');
            $qb->leftJoin('c.sessions', 'lm_session');
            $qb->leftJoin('lm_session.learningMaterials', 'lm_slm');
            $qb->leftJoin('lm_slm.learningMaterial', 'lm_lm2');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('lm_lm.id', ':lms'),
                $qb->expr()->in('lm_lm2.id', ':lms')
            ));
            $qb->setParameter(':lms', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->join('c.objectives', 'c_course_objective');
            $qb->join('c_course_objective.parents', 'c_program_year_objective');
            $qb->leftJoin('c_program_year_objective.competency', 'c_competency');
            $qb->leftJoin('c_competency.parent', 'c_competency2');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('c_competency.id', ':competencies'),
                $qb->expr()->in('c_competency2.id', ':competencies')
            ));
            $qb->setParameter(':competencies', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors']) ?
                $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('c.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('c.sessions', 'm_session');
            $qb->leftJoin('m_session.meshDescriptors', 'm_sessMeshDescriptor');
            $qb->leftJoin('c.objectives', 'm_cObjective');
            $qb->leftJoin('m_cObjective.meshDescriptors', 'm_cObjectiveMeshDescriptor');
            $qb->leftJoin('m_session.objectives', 'm_sObjective');
            $qb->leftJoin('m_sObjective.meshDescriptors', 'm_sObjectiveMeshDescriptors');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('m_meshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_sessMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_cObjectiveMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('m_sObjectiveMeshDescriptors.id', ':meshDescriptors')
            ));
            $qb->setParameter(':meshDescriptors', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('c.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['sessions']);
        unset($criteria['topics']);
        unset($criteria['programs']);
        unset($criteria['programYears']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("c.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getYears()
    {
        $dql = 'SELECT DISTINCT c.year FROM IliosCoreBundle:Course c ORDER BY c.year ASC';
        $results = $this->getEntityManager()->createQuery($dql)->getArrayResult();

        $return = [];
        foreach ($results as $arr) {
            $return[] = $arr['year'];
        }

        return $return;
    }

    /**
     * Checks if a given user is assigned as instructor to ILMs or offerings in a given course.
     *
     * @param UserInterface $user
     * @param CourseInterface $course
     * @return boolean TRUE if the user instructs at least one offering or ILM, FALSE otherwise.
     */
    public function isUserInstructingInCourse(UserInterface $user, CourseInterface $course)
    {
        $sql =<<<EOL
SELECT
  oxi.user_id
FROM
  offering_x_instructor oxi
JOIN offering o ON o.offering_id = oxi.offering_id
JOIN session s ON s.session_id = o.session_id
WHERE
  oxi.user_id = :user_id
  AND s.course_id = :course_id

UNION

SELECT
  igxu.user_id
FROM 
  instructor_group_x_user igxu 
JOIN offering_x_instructor_group oxig ON oxig.instructor_group_id = igxu.instructor_group_id
JOIN offering o ON o.offering_id = oxig.offering_id
JOIN session s ON s.session_id = o.session_id
WHERE 
  igxu.user_id = :user_id
  AND s.course_id = :course_id

UNION

SELECT 
  ixi.user_id
FROM 
  ilm_session_facet_x_instructor ixi
JOIN ilm_session_facet i ON i.ilm_session_facet_id = ixi.ilm_session_facet_id
JOIN session s ON s.session_id = i.session_id
WHERE
  ixi.user_id = :user_id
  AND s.course_id = :course_id

UNION

SELECT
  igxu.user_id
FROM
  instructor_group_x_user igxu
JOIN ilm_session_facet_x_instructor_group ixig ON ixig.instructor_group_id = igxu.instructor_group_id
JOIN ilm_session_facet i ON i.ilm_session_facet_id = ixig.ilm_session_facet_id
JOIN session s ON s.session_id = i.session_id
WHERE
  igxu.user_id = :user_id
  AND s.course_id = :course_id
EOL;

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("user_id", $user->getId());
        $stmt->bindValue("course_id", $course->getId());
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        $isInstructing = ! empty($rows);
        $stmt->closeCursor();
        return $isInstructing;
    }

    /**
     * Finds all courses associated with a given user.
     * A user can be associated as either course director, learner or instructor with a given course.
     *
     * @param \Ilios\CoreBundle\Entity\UserInterface $user
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByUser(
        UserInterface $user,
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null)
    {
        $sql =<<<EOL
SELECT c.* FROM course c
  JOIN course_director cd ON cd.course_id = c.course_id
  JOIN user u ON u.user_id = cd.course_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN offering o ON o.session_id = s.session_id
  JOIN offering_x_learner oxl ON oxl.offering_id = o.offering_id
  JOIN user u ON u.user_id = oxl.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN offering o ON o.session_id = s.session_id
  JOIN offering_x_group oxg ON oxg.offering_id = o.offering_id
  JOIN `group` g ON g.group_id = oxg.group_id
  JOIN group_x_user gxu ON gxu.group_id = g.group_id
  JOIN user u ON u.user_id = gxu.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
  JOIN ilm_session_facet_x_learner ilmxl ON ilmxl.ilm_session_facet_id = ilm.ilm_session_facet_id
  JOIN user u ON u.user_id = ilmxl.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
  JOIN ilm_session_facet_x_group ilmxg ON ilmxg.ilm_session_facet_id = ilm.ilm_session_facet_id
  JOIN `group` g ON g.group_id = ilmxg.group_id
  JOIN group_x_user gxu ON gxu.group_id = g.group_id
  JOIN user u ON u.user_id = gxu.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN offering o ON o.session_id = s.session_id
  JOIN offering_x_instructor oxi ON oxi.offering_id = o.offering_id
  JOIN user u ON u.user_id = oxi.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN offering o ON o.session_id = s.session_id
  JOIN offering_x_instructor_group oxig ON oxig.offering_id = o.offering_id
  JOIN `group` g ON g.group_id = oxig.instructor_group_id
  JOIN group_x_user gxu ON gxu.group_id = g.group_id
  JOIN user u ON u.user_id = gxu.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
  JOIN ilm_session_facet_x_instructor ilmxi ON ilmxi.ilm_session_facet_id = ilm.ilm_session_facet_id
  JOIN user u ON u.user_id = ilmxi.user_id
  WHERE u.user_id = :user_id
UNION
SELECT c.* FROM course c
  JOIN `session` s ON s.course_id = c.course_id
  JOIN ilm_session_facet ilm ON ilm.session_id = s.session_id
  JOIN ilm_session_facet_x_instructor_group ilmxig ON ilmxig.ilm_session_facet_id = ilm.ilm_session_facet_id
  JOIN `group` g ON g.group_id = ilmxig.instructor_group_id
  JOIN group_x_user gxu ON gxu.group_id = g.group_id
  JOIN user u ON u.user_id = gxu.user_id
  WHERE u.user_id = :user_id
EOL;

        // @todo Add property filter criteria to query. [ST 2015/12/18]
        // @todo Add ORDER BY clause(s) to query. [ST 2015/12/18]

        if (isset($limit)) {
            $sql .= ' LIMIT :limit';
        }

        if (isset($offset)) {
            $sql .= ' OFFSET :offset';
        }

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("user_id", $user->getId());

        // @todo Bind criteria values. [ST 2015/12/18]
        // @todo Bind order-by values. [ST 2015/12/18]

        if (isset($limit)) {
            $stmt->bindValue('limit', $limit);
        }
        if (isset($offset)) {
            $stmt->bindValue('offset', $offset);
        }
        $stmt->execute();
    }
}
