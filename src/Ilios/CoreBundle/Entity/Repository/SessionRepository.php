<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class SessionRepository extends EntityRepository
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

        $qb->select('DISTINCT s')->from('IliosCoreBundle:Session', 's');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('s.'.$sort, $order);
            }
        }
        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->join('s.topics', 'topic');
            $qb->andWhere($qb->expr()->in('topic.id', ':topics'));
            $qb->setParameter(':topics', $ids);
        }
        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('s.offerings', 'offering');
            $qb->leftJoin('offering.instructors', 'user');
            $qb->leftJoin('offering.instructorGroups', 'insGroup');
            $qb->leftJoin('insGroup.users', 'groupUser');
            $qb->leftJoin('s.ilmSession', 'ilmSession');
            $qb->leftJoin('ilmSession.instructors', 'ilmInstructor');
            $qb->leftJoin('ilmSession.instructorGroups', 'ilmInsGroup');
            $qb->leftJoin('ilmInsGroup.users', 'ilmInsGroupUser');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('user.id', ':users'),
                    $qb->expr()->in('groupUser.id', ':users'),
                    $qb->expr()->in('ilmInstructor.id', ':users'),
                    $qb->expr()->in('ilmInsGroupUser.id', ':users')
                )
            );
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups']) ?
                $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('s.offerings', 'offering');
            $qb->leftJoin('offering.instructorGroups', 'igroup');
            $qb->leftJoin('s.ilmSession', 'ilmSession');
            $qb->leftJoin('ilmSession.instructorGroups', 'ilmInsGroup');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('igroup.id', ':igroups'),
                    $qb->expr()->in('ilmInsGroup.id', ':igroups')
                )
            );
            $qb->setParameter(':igroups', $ids);
        }
        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ?
                $criteria['learningMaterials'] : [$criteria['learningMaterials']];

            $qb->leftJoin('s.learningMaterials', 'slm');
            $qb->leftJoin('slm.learningMaterial', 'lm');
            $qb->andWhere($qb->expr()->in('lm.id', ':lms'));

            $qb->setParameter(':lms', $ids);
        }
        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('s.course', 'course');
            $qb->join('course.cohorts', 'cohort');
            $qb->join('cohort.programYear', 'programYear');
            $qb->join('programYear.program', 'program');

            $qb->andWhere($qb->expr()->in('program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }
        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->leftJoin('s.objectives', 'objective');
            $qb->leftJoin('objective.parents', 'parent');

            $qb->leftJoin('parent.competency', 'competency');

            $qb->andWhere($qb->expr()->in('competency.id', ':competencies'));
            $qb->setParameter(':competencies', $ids);
        }
        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors']) ?
                $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];

            $qb->leftJoin('s.meshDescriptors', 'meshDescriptor');
            $qb->leftJoin('s.objectives', 'objective');
            $qb->leftJoin('objective.meshDescriptors', 'objectiveMeshDescriptor');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('meshDescriptor.id', ':meshDescriptors'),
                    $qb->expr()->in('objectiveMeshDescriptor.id', ':meshDescriptors')
                )
            );

            $qb->setParameter(':meshDescriptors', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sessions']);
        unset($criteria['topics']);
        unset($criteria['programs']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("s.{$key}", ":{$key}"));
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
}
