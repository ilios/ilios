<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class InstructorGroupRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class InstructorGroupRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();


        $qb->select('DISTINCT i')->from('IliosCoreBundle:InstructorGroup', 'i');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('i.'.$sort, $order);
            }
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('i.ilmSessions', 'c_ilm');
            $qb->leftJoin('i.offerings', 'c_offering');
            $qb->leftJoin('c_ilm.session', 'c_session');
            $qb->leftJoin('c_offering.session', 'c_session2');
            $qb->leftJoin('c_session.course', 'c_course');
            $qb->leftJoin('c_session2.course', 'c_course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('c_course.id', ':courses'),
                    $qb->expr()->in('c_course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('i.ilmSessions', 'se_ilm');
            $qb->leftJoin('i.offerings', 'se_offering');
            $qb->leftJoin('se_ilm.session', 'se_session');
            $qb->leftJoin('se_offering.session', 'se_session2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('se_session.id', ':sessions'),
                    $qb->expr()->in('se_session2.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->leftJoin('i.ilmSessions', 'st_ilm');
            $qb->leftJoin('i.offerings', 'st_offering');
            $qb->leftJoin('st_ilm.session', 'st_session');
            $qb->leftJoin('st_offering.session', 'st_session2');
            $qb->leftJoin('st_session.sessionType', 'st_sessionType');
            $qb->leftJoin('st_session2.sessionType', 'st_sessionType2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('st_sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('st_sessionType2.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->join('i.users', 'i_instructor');
            $qb->andWhere($qb->expr()->in('i_instructor.id', ':instructors'));
            $qb->setParameter(':instructors', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials'])
                ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('i.ilmSessions', 'lm_ilm');
            $qb->leftJoin('i.offerings', 'lm_offering');
            $qb->leftJoin('lm_ilm.session', 'lm_session');
            $qb->leftJoin('lm_offering.session', 'lm_session2');
            $qb->leftJoin('lm_session.learningMaterials', 'lm_slm');
            $qb->leftJoin('lm_session2.learningMaterials', 'lm_slm2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('lm_slm.id', ':learningMaterials'),
                    $qb->expr()->in('lm_slm2.id', ':learningMaterials')
                )
            );
            $qb->setParameter(':learningMaterials', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics'])
                ? $criteria['topics'] : [$criteria['topics']];
            $qb->leftJoin('i.ilmSessions', 't_ilm');
            $qb->leftJoin('i.offerings', 't_offering');
            $qb->leftJoin('t_ilm.session', 't_session');
            $qb->leftJoin('t_offering.session', 't_session2');
            $qb->leftJoin('t_session.topics', 't_topic');
            $qb->leftJoin('t_session2.topics', 't_topic2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_topic.id', ':topics'),
                    $qb->expr()->in('t_topic2.id', ':topics')
                )
            );
            $qb->setParameter(':topics', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('i.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['instructors']);
        unset($criteria['learningMaterials']);
        unset($criteria['topics']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("i.{$key}", ":{$key}"));
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
