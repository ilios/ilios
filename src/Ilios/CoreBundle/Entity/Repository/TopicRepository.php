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
            $qb->leftJoin('t.courses', 'cr_course');
            $qb->leftJoin('t.sessions', 'cr_session');
            $qb->leftJoin('cr_session.course', 'cr_course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('cr_course.id', ':courses'),
                    $qb->expr()->in('cr_course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('t.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->join('t.sessions', 'st_session');
            $qb->join('st_session.sessionType', 'st_sessionType');
            $qb->andWhere($qb->expr()->in('st_sessionType.id', ':sessionTypes'));
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            $qb->join('t.programYears', 'p_programYear');
            $qb->join('p_programYear.program', 'p_program');
            $qb->andWhere($qb->expr()->in('p_program.id', ':programs'));
            $qb->setParameter(':programs', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->join('t.sessions', 'i_session');
            $qb->leftJoin('i_session.offerings', 'i_offering');
            $qb->leftJoin('i_offering.instructors', 'i_instructor');
            $qb->leftJoin('i_offering.instructorGroups', 'i_iGroup');
            $qb->leftJoin('i_iGroup.users', 'i_instructor2');
            $qb->leftJoin('i_session.ilmSession', 'i_ilm');
            $qb->leftJoin('i_ilm.instructors', 'i_instructor3');
            $qb->leftJoin('i_ilm.instructorGroups', 'i_iGroup2');
            $qb->leftJoin('i_iGroup2.users', 'i_instructor4');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('i_instructor.id', ':instructors'),
                    $qb->expr()->in('i_instructor2.id', ':instructors'),
                    $qb->expr()->in('i_instructor3.id', ':instructors'),
                    $qb->expr()->in('i_instructor4.id', ':instructors')
                )
            );
            $qb->setParameter(':instructors', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups'])
                ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->join('t.sessions', 'ig_session');
            $qb->leftJoin('ig_session.offerings', 'ig_offering');
            $qb->leftJoin('ig_offering.instructorGroups', 'ig_iGroup');
            $qb->leftJoin('ig_session.ilmSession', 'ig_ilm');
            $qb->leftJoin('ig_ilm.instructorGroups', 'ig_iGroup2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('ig_iGroup.id', ':iGroups'),
                    $qb->expr()->in('ig_iGroup2.id', ':iGroups')
                )
            );
            $qb->setParameter(':iGroups', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials'])
                ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('t.courses', 'lm_course');
            $qb->leftJoin('t.sessions', 'lm_session');
            $qb->leftJoin('lm_course.learningMaterials', 'lm_clm');
            $qb->leftJoin('lm_session.learningMaterials', 'lm_slm');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('lm_slm.id', ':learningMaterials'),
                    $qb->expr()->in('lm_clm.id', ':learningMaterials')
                )
            );
            $qb->setParameter(':learningMaterials', $ids);
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            $qb->leftJoin('t.courses', 'cm_course');
            $qb->leftJoin('t.sessions', 'cm_session');
            $qb->leftJoin('cm_course.objectives', 'cm_course_objective');
            $qb->leftJoin('cm_course_objective.parents', 'cm_program_year_objective');
            $qb->leftJoin('cm_program_year_objective.competency', 'cm_competency');
            $qb->leftJoin('cm_competency.parent', 'cm_competency2');
            $qb->leftJoin('cm_session.objectives', 'cm_session_objective');
            $qb->leftJoin('cm_session_objective.parents', 'cm_course_objective2');
            $qb->leftJoin('cm_course_objective2.parents', 'cm_program_year_objective2');
            $qb->leftJoin('cm_program_year_objective2.competency', 'cm_competency3');
            $qb->leftJoin('cm_competency3.parent', 'cm_competency4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('cm_competency.id', ':competencies'),
                $qb->expr()->in('cm_competency2.id', ':competencies'),
                $qb->expr()->in('cm_competency3.id', ':competencies'),
                $qb->expr()->in('cm_competency4.id', ':competencies')
            ));
            $qb->setParameter(':competencies', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors'])
                ? $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('t.courses', 'm_course');
            $qb->leftJoin('t.sessions', 'm_session');
            $qb->leftJoin('m_course.meshDescriptors', 'm_meshDescriptor');
            $qb->leftJoin('m_session.meshDescriptors', 'm_meshDescriptor2');
            $qb->leftJoin('m_session.course', 'm_course2');
            $qb->leftJoin('m_course2.meshDescriptors', 'm_meshDescriptor3');
            $qb->leftJoin('m_course.learningMaterials', 'm_clm');
            $qb->leftJoin('m_clm.meshDescriptors', 'm_meshDescriptor4');
            $qb->leftJoin('m_session.learningMaterials', 'm_slm');
            $qb->leftJoin('m_slm.meshDescriptors', 'm_meshDescriptor5');
            $qb->leftJoin('m_course2.learningMaterials', 'm_clm2');
            $qb->leftJoin('m_clm.meshDescriptors', 'm_meshDescriptor6');
            $qb->leftJoin('m_course.objectives', 'm_objective');
            $qb->leftJoin('m_objective.meshDescriptors', 'm_meshDescriptor7');
            $qb->leftJoin('m_session.objectives', 'm_objective2');
            $qb->leftJoin('m_objective2.meshDescriptors', 'm_meshDescriptor8');
            $qb->leftJoin('m_course2.objectives', 'm_objective3');
            $qb->leftJoin('m_objective3.meshDescriptors', 'm_meshDescriptor9');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('m_meshDescriptor.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor2.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor3.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor4.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor5.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor6.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor7.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor8.id', ':meshDescriptors'),
                    $qb->expr()->in('m_meshDescriptor9.id', ':meshDescriptors')
                )
            );
            $qb->setParameter(':meshDescriptors', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('t.sessions', 'sc_session');
            $qb->join('sc_session.course', 'sc_course');
            $qb->join('sc_course.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        unset($criteria['schools']);
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
