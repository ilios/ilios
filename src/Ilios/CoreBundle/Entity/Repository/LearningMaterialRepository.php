<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

class LearningMaterialRepository extends EntityRepository
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

        $qb->select('DISTINCT lm')->from('IliosCoreBundle:LearningMaterial', 'lm');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('lm.' . $sort, $order);
            }
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('lm.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('lm.courseLearningMaterials', 'clm');
            $qb->leftJoin('clm.course', 'course');
            $qb->andWhere($qb->expr()->in('course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }
        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->leftJoin('lm.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructors', 'user');
            $qb->leftJoin('offering.instructorGroups', 'insGroup');
            $qb->leftJoin('insGroup.users', 'groupUser');
            $qb->leftJoin('session.ilmSession', 'ilmSession');
            $qb->leftJoin('ilmSession.instructors', 'ilmInstructor');
            $qb->leftJoin('ilmSession.instructorGroups', 'ilmInsGroup');
            $qb->leftJoin('ilmInsGroup.users', 'ilmInsGroupUser');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('user.id', ':users'),
                $qb->expr()->in('groupUser.id', ':users'),
                $qb->expr()->in('ilmInstructor.id', ':users'),
                $qb->expr()->in('ilmInsGroupUser.id', ':users')
            ));
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups']) ?
                $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->leftJoin('lm.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session');
            $qb->leftJoin('session.offerings', 'offering');
            $qb->leftJoin('offering.instructorGroups', 'igroup');
            $qb->leftJoin('session.ilmSession', 'ilmSession');
            $qb->leftJoin('ilmSession.instructorGroups', 'ilmInsGroup');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('igroup.id', ':igroups'),
                    $qb->expr()->in('ilmInsGroup.id', ':igroups')
                )
            );
            $qb->setParameter(':igroups', $ids);
        }
        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->leftJoin('lm.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session');
            $qb->leftJoin('session.topics', 'sessionTopic');
            $qb->leftJoin('lm.courseLearningMaterials', 'clm');
            $qb->leftJoin('clm.course', 'course');
            $qb->leftJoin('course.topics', 'courseTopic');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('courseTopic.id', ':topics'),
                $qb->expr()->in('sessionTopic.id', ':topics')
            ));
            $qb->setParameter(':topics', $ids);
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors']) ?
                $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            $qb->leftJoin('lm.sessionLearningMaterials', 'slm');
            $qb->leftJoin('lm.courseLearningMaterials', 'clm');

            $qb->leftJoin('slm.meshDescriptors', 'slmMeshDescriptor');
            $qb->leftJoin('clm.meshDescriptors', 'clmMeshDescriptor');


            $qb->leftJoin('slm.session', 'session');
            $qb->leftJoin('clm.course', 'course');

            $qb->leftJoin('session.meshDescriptors', 'sessMeshDescriptor');
            $qb->leftJoin('course.meshDescriptors', 'courseMeshDescriptor');

            $qb->leftJoin('session.objectives', 'sObjective');
            $qb->leftJoin('sObjective.meshDescriptors', 'sObjectiveMeshDescriptors');

            $qb->leftJoin('course.objectives', 'cObjective');
            $qb->leftJoin('cObjective.meshDescriptors', 'cObjectiveMeshDescriptors');

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('slmMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('clmMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('sessMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('courseMeshDescriptor.id', ':meshDescriptors'),
                $qb->expr()->in('cObjectiveMeshDescriptors.id', ':meshDescriptors'),
                $qb->expr()->in('sObjectiveMeshDescriptors.id', ':meshDescriptors')
            ));

            $qb->setParameter(':meshDescriptors', $ids);
        }
        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->leftJoin('lm.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session');
            $qb->leftJoin('session.sessionType', 'sessionType');
            $qb->andWhere($qb->expr()->in('sessionType.id', ':sessionTypes'));

            $qb->setParameter(':sessionTypes', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['topics']);
        unset($criteria['meshDescriptors']);
        unset($criteria['sessionTypes']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("lm.{$key}", ":{$key}"));
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
     * Find all the file type learning materials
     */
    public function findFileLearningMaterials()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'lm')->from('IliosCoreBundle:LearningMaterial', 'lm');
        $qb->where($qb->expr()->isNotNull('lm.relativePath'));

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by a string query
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return LearningMaterialInterface[]
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'lm')->from('IliosCoreBundle:LearningMaterial', 'lm');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('lm.title', "?{$key}"),
                $qb->expr()->like('lm.description', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('lm.' . $sort, $order);
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
