<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TopicRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class TopicRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();


        $qb->select('DISTINCT t')->from('IliosCoreBundle:Topic', 't');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('t.'.$sort, $order);
            }
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('t.courses', 'course');
            $qb->leftJoin('t.sessions', 'session');
            $qb->leftJoin('session.course', 'course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('course.id', ':courses'),
                    $qb->expr()->in('course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('t.sessions', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->join('t.sessions', 'session');
            $qb->join('session.sessionType', 'sessionType');
            $qb->andWhere($qb->expr()->in('sessionType.id', ':sessionTypes'));
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('t.programYears', 'programYear');
            $qb->join('programYear.program', 'program');
            $qb->andWhere($qb->expr()->in('program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->join('t.sessions', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructors', 'instructor');
            $qb->leftJoin('offering.instructorGroups', 'iGroup');
            $qb->leftJoin('iGroup.users', 'instructor2');
            $qb->leftJoin('session.ilmSession', 'ilm');
            $qb->leftJoin('ilm.instructors', 'instructor3');
            $qb->leftJoin('ilm.instructorGroups', 'iGroup2');
            $qb->leftJoin('iGroup2.users', 'instructor4');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('instructor.id', ':instructors'),
                    $qb->expr()->in('instructor2.id', ':instructors'),
                    $qb->expr()->in('instructor3.id', ':instructors'),
                    $qb->expr()->in('instructor4.id', ':instructors')
                )
            );
            $qb->setParameter(':instructors', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups'])
                ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->join('t.sessions', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructorGroups', 'iGroup');
            $qb->leftJoin('session.ilmSession', 'ilm');
            $qb->leftJoin('ilm.instructorGroups', 'iGroup2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('iGroup.id', ':iGroups'),
                    $qb->expr()->in('iGroup2.id', ':iGroups')
                )
            );
            $qb->setParameter(':iGroups', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials'])
                ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('t.courses', 'course');
            $qb->leftJoin('t.sessions', 'session');
            $qb->leftJoin('course.learningMaterials', 'clm');
            $qb->leftJoin('session.learningMaterials', 'slm');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('slm.id', ':learningMaterials'),
                    $qb->expr()->in('clm.id', ':learningMaterials')
                )
            );
            $qb->setParameter(':learningMaterials', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->leftJoin('t.courses', 'course');
            $qb->leftJoin('t.sessions', 'session');
            $qb->leftJoin('course.objectives', 'objective');
            $qb->leftJoin('objective.competency', 'competency');
            $qb->leftJoin('session.objectives', 'objective2');
            $qb->leftJoin('objective2.parents', 'objective3');
            $qb->leftJoin('objective3.competency', 'competency2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('competency.id', ':competencies'),
                    $qb->expr()->in('competency2.id', ':competencies')
                )
            );
            $qb->setParameter(':competencies', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors'])
                ? $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('t.courses', 'course');
            $qb->leftJoin('t.sessions', 'session');
            $qb->leftJoin('course.meshDescriptors', 'meshDescriptor');
            $qb->leftJoin('session.meshDescriptors', 'meshDescriptor2');
            $qb->leftJoin('session.course', 'course2');
            $qb->leftJoin('course2.meshDescriptors', 'meshDescriptor3');
            $qb->leftJoin('course.learningMaterials', 'clm');
            $qb->leftJoin('clm.meshDescriptors', 'meshDescriptor4');
            $qb->leftJoin('session.learningMaterials', 'slm');
            $qb->leftJoin('slm.meshDescriptors', 'meshDescriptor5');
            $qb->leftJoin('course2.learningMaterials', 'clm2');
            $qb->leftJoin('clm.meshDescriptors', 'meshDescriptor6');
            $qb->leftJoin('course.objectives', 'objective');
            $qb->leftJoin('objective.meshDescriptors', 'meshDescriptor7');
            $qb->leftJoin('session.objectives', 'objective2');
            $qb->leftJoin('objective2.meshDescriptors', 'meshDescriptor8');
            $qb->leftJoin('course2.objectives', 'objective3');
            $qb->leftJoin('objective3.meshDescriptors', 'meshDescriptor9');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('meshDescriptor.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor2.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor3.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor4.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor5.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor6.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor7.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor8.id', ':meshDescriptors'),
                    $qb->expr()->in('meshDescriptor9.id', ':meshDescriptors')
                )
            );
            $qb->setParameter(':meshDescriptors', $ids);

        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['programs']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("t.{$key}", ":{$key}"));
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
