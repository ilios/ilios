<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

class CourseRepository extends EntityRepository
{
    /**
     * Custrom findBy so we can filter by related entities
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
            $qb->leftJoin('c.sessions', 'session');
            $qb->orWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }
        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->join('c.topics', 'topic');
            $qb->orWhere($qb->expr()->in('topic.id', ':topics'));
            $qb->setParameter(':topics', $ids);
        }
        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('c.cohorts', 'cohort');
            $qb->join('cohort.programYear', 'programYear');
            $qb->join('programYear.program', 'program');

            $qb->andWhere($qb->expr()->in('program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }
        if (array_key_exists('programYears', $criteria)) {
            $ids = is_array($criteria['programYears']) ? $criteria['programYears'] : [$criteria['programYears']];
            $qb->join('c.cohorts', 'cohort');
            $qb->join('cohort.programYear', 'programYear');

            $qb->orWhere($qb->expr()->in('programYear.id', ':programYears'));
            $qb->setParameter(':programYears', $ids);
        }
        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('c.sessions', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructors', 'user');
            $qb->orWhere($qb->expr()->in('user.id', ':users'));

            $qb->leftJoin('offering.instructorGroups', 'insGroup');
            $qb->leftJoin('insGroup.users', 'groupUser');
            $qb->orWhere($qb->expr()->in('groupUser.id', ':users'));

            $qb->setParameter(':users', $ids);

        }
        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups']) ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('c.sessions', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructorGroups', 'igroup');

            $qb->orWhere($qb->expr()->in('igroup.id', ':igroups'));
            $qb->setParameter(':igroups', $ids);
        }
        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('c.learningMaterials', 'clm');
            $qb->leftJoin('clm.learningMaterial', 'lm');
            $qb->orWhere($qb->expr()->in('lm.id', ':lms'));

            $qb->leftJoin('c.sessions', 'session');
            $qb->leftJoin('session.learningMaterials', 'slm');
            $qb->leftJoin('slm.learningMaterial', 'lm2');

            $qb->orWhere($qb->expr()->in('lm2.id', ':lms'));

            $qb->setParameter(':lms', $ids);
        }
        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->leftJoin('c.objectives', 'objective');
            $qb->leftJoin('objective.competency', 'competency');

            $qb->orWhere($qb->expr()->in('competency.id', ':competencies'));
            $qb->setParameter(':competencies', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sessions']);
        unset($criteria['topics']);
        unset($criteria['programs']);
        unset($criteria['programYears']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);


        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $qb->orWhere($qb->expr()->like("c.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $value);
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
