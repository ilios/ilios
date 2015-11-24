<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class SessionTypeRepository extends EntityRepository
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


        $qb->select('DISTINCT st')->from('IliosCoreBundle:SessionType', 'st');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('st.'.$sort, $order);
            }
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('st.sessions', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('st.sessions', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructors', 'instructor');
            $qb->leftJoin('offering.instructorGroups', 'insGroup');
            $qb->leftJoin('insGroup.users', 'insGroupUser');
            $qb->leftJoin('session.ilmSession', 'ilmSession');
            $qb->leftJoin('ilmSession.instructors', 'ilmInstructor');
            $qb->leftJoin('ilmSession.instructorGroups', 'ilmInsGroup');
            $qb->leftJoin('ilmInsGroup.users', 'ilmInsGroupUser');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('instructor.id', ':users'),
                    $qb->expr()->in('insGroupUser.id', ':users'),
                    $qb->expr()->in('ilmInstructor.id', ':users'),
                    $qb->expr()->in('ilmInsGroupUser.id', ':users')
                )
            );
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups'])
                ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('st.sessions', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructorGroups', 'insGroup');
            $qb->leftJoin('session.ilmSession', 'ilmSession');
            $qb->leftJoin('ilmSession.instructorGroups', 'ilmInsGroup');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('insGroup.id', ':igroups'),
                    $qb->expr()->in('ilmInsGroup.id', ':igroups')
                )
            );
            $qb->setParameter(':igroups', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->leftJoin('st.sessions', 'session');
            $qb->leftJoin('session.objectives', 'objective');
            $qb->leftJoin('objective.parents', 'parent');
            $qb->leftJoin('parent.competency', 'competency');
            $qb->andWhere($qb->expr()->in('competency.id', ':competencies'));
            $qb->setParameter(':competencies', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors']) ?
                $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('st.sessions', 'session');
            $qb->leftJoin('session.meshDescriptors', 'meshDescriptor');
            $qb->leftJoin('session.objectives', 'objective');
            $qb->leftJoin('objective.meshDescriptors', 'objectiveMeshDescriptor');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('meshDescriptor.id', ':meshDescriptors'),
                    $qb->expr()->in('objectiveMeshDescriptor.id', ':meshDescriptors')
                )
            );
            $qb->setParameter(':meshDescriptors', $ids);
        }

        unset($criteria['sessions']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("st.{$key}", ":{$key}"));
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
