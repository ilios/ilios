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
            $qb->leftJoin('c.sessions', 'se_session');
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

        if (array_key_exists('users', $criteria)) {
            $ids = is_array($criteria['users']) ? $criteria['users'] : [$criteria['users']];
            $qb->leftJoin('c.directors', 'u_courseDirector');
            $qb->leftJoin('c.sessions', 'u_session');
            $qb->leftJoin('u_session.offerings', 'u_offering');
            $qb->leftJoin('u_session.ilmSession', 'u_ilmSession');
            $qb->leftJoin('u_offering.instructors', 'u_instructor');
            $qb->leftJoin('u_offering.learners', 'u_learner');
            $qb->leftJoin('u_offering.instructorGroups', 'u_insGroup');
            $qb->leftJoin('u_insGroup.users', 'u_igUser');
            $qb->leftJoin('u_offering.learnerGroups', 'u_learnerGroup');
            $qb->leftJoin('u_learnerGroup.users', 'u_lgUser');
            $qb->leftJoin('u_ilmSession.instructors', 'u_ilmInstructor');
            $qb->leftJoin('u_ilmSession.learners', 'u_ilmLearner');
            $qb->leftJoin('u_ilmSession.instructorGroups', 'u_ilmInsGroup');
            $qb->leftJoin('u_ilmInsGroup.users', 'u_ilmIgUser');
            $qb->leftJoin('u_ilmSession.learnerGroups', 'u_ilmLearnerGroup');
            $qb->leftJoin('u_ilmLearnerGroup.users', 'u_ilmLgUser');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('u_learner.id', ':users'),
                $qb->expr()->in('u_instructor.id', ':users'),
                $qb->expr()->in('u_courseDirector.id', ':users'),
                $qb->expr()->in('u_igUser.id', ':users'),
                $qb->expr()->in('u_lgUser.id', ':users'),
                $qb->expr()->in('u_ilmLearner.id', ':users'),
                $qb->expr()->in('u_ilmInstructor.id', ':users'),
                $qb->expr()->in('u_ilmIgUser.id', ':users'),
                $qb->expr()->in('u_ilmLgUser.id', ':users')
            ));
            $qb->setParameter(':users', $ids);
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
            $qb->leftJoin('c.objectives', 'c_objective');
            $qb->leftJoin('c_objective.competency', 'c_competency');
            $qb->andWhere($qb->expr()->in('c_competency.id', ':competencies'));
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

        //cleanup all the possible relationship filters
        unset($criteria['sessions']);
        unset($criteria['topics']);
        unset($criteria['programs']);
        unset($criteria['programYears']);
        unset($criteria['users']);
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
}
