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
        $qb->add('select', 'c')->from('IliosCoreBundle:Course', 'c');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('c.' . $sort, $order);
            }
        }
        if (array_key_exists('topics', $criteria)) {
            $topicIds = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->join('c.topics', 'topic');
            $qb->andWhere($qb->expr()->in('topic.id', ':topics'));
            $qb->setParameter(':topics', $topicIds);
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
