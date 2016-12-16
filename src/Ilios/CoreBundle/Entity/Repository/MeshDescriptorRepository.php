<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Class MeshDescriptorRepository
 */
class MeshDescriptorRepository extends EntityRepository implements DTORepositoryInterface
{

    /**
     * Find by a string query.
     *
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return MeshDescriptorInterface[]
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('DISTINCT d')
            ->from('IliosCoreBundle:MeshDescriptor', 'd')
            ->leftJoin('d.previousIndexing', 'pi')
            ->leftJoin('d.concepts', 'c')
            ->leftJoin('c.semanticTypes', 'st')
            ->leftJoin('c.terms', 't');

        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('d.id', "?{$key}"),
                $qb->expr()->like('d.name', "?{$key}"),
                $qb->expr()->like('d.annotation', "?{$key}"),
                $qb->expr()->like('pi.previousIndexing', "?{$key}"),
                $qb->expr()->like('st.name', "?{$key}"),
                $qb->expr()->like('t.name', "?{$key}"),
                $qb->expr()->like('c.name', "?{$key}"),
                $qb->expr()->like('c.scopeNote', "?{$key}"),
                $qb->expr()->like('c.casn1Name', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }
        if (empty($orderBy)) {
            $orderBy = ['name' => 'ASC', 'id' => 'ASC'];
        }

        foreach ($orderBy as $sort => $order) {
            $qb->addOrderBy('d.' . $sort, $order);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }
        $query = $qb->getQuery();
        $query->useResultCache(true);
        
        $results = $query->getResult();
        
        // Unfortunately, we can't let Doctrine limit the fetch here because of all the joins
        // it returns many less than the desired number.
        if ($limit) {
            $results = array_slice($results, 0, $limit);
        }

        return $results;
    }

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

        $qb->select('DISTINCT m')->from('IliosCoreBundle:MeshDescriptor', 'm');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('m')->distinct()->from('IliosCoreBundle:MeshDescriptor', 'm');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $descriptorDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $descriptorDTOs[$arr['id']] = new MeshDescriptorDTO(
                $arr['id'],
                $arr['name'],
                $arr['annotation'],
                $arr['createdAt'],
                $arr['updatedAt'],
                $arr['deleted']
            );
        }
        $descriptorIds = array_keys($descriptorDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('p.id AS prevId, m.id AS descriptorId')
            ->from('IliosCoreBundle:MeshPreviousIndexing', 'p')
            ->join('p.descriptor', 'm')
            ->where($qb->expr()->in('m.id', ':descriptorIds'))
            ->setParameter('descriptorIds', $descriptorIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $descriptorDTOs[$arr['descriptorId']]->previousIndexing = (int) $arr['prevId'];
        }

        $related = [
            'courses',
            'objectives',
            'sessions',
            'concepts',
            'qualifiers',
            'trees',
            'sessionLearningMaterials',
            'courseLearningMaterials',
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, m.id AS descriptorId')->from('IliosCoreBundle:MeshDescriptor', 'm')
                ->join("m.{$rel}", 'r')
                ->where($qb->expr()->in('m.id', ':descriptorIds'))
                ->orderBy('relId')
                ->setParameter('descriptorIds', $descriptorIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $descriptorDTOs[$arr['descriptorId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($descriptorDTOs);
    }

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ?
                $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session2');
            $qb->leftJoin('objective.sessions', 'session3');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('session.id', ':sessions'),
                    $qb->expr()->in('session2.id', ':sessions'),
                    $qb->expr()->in('session3.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ?
                $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('m.courses', 'course');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.courseLearningMaterials', 'clm');
            $qb->leftJoin('objective.courses', 'course2');
            $qb->leftJoin('clm.course', 'course3');
            $qb->leftJoin('session.course', 'course4');
            $qb->leftJoin('objective.sessions', 'session2');
            $qb->leftJoin('session2.course', 'course5');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session3');
            $qb->leftJoin('session3.course', 'course6');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('course.id', ':courses'),
                    $qb->expr()->in('course2.id', ':courses'),
                    $qb->expr()->in('course3.id', ':courses'),
                    $qb->expr()->in('course4.id', ':courses'),
                    $qb->expr()->in('course5.id', ':courses'),
                    $qb->expr()->in('course6.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ?
                $criteria['sessionTypes'] : [$criteria['sessionTypes']];

            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('session.sessionType', 'sessionType');
            $qb->leftJoin('slm.session', 'session2');
            $qb->leftJoin('session2.sessionType', 'sessionType2');
            $qb->leftJoin('objective.sessions', 'session3');
            $qb->leftJoin('session3.sessionType', 'sessionType3');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('sessionType2.id', ':sessionTypes'),
                    $qb->expr()->in('sessionType3.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials']) ?
                $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('m.courseLearningMaterials', 'clm');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.learningMaterial', 'learningMaterial');
            $qb->leftJoin('clm.learningMaterial', 'learningMaterial2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('learningMaterial.id', ':lm'),
                    $qb->expr()->in('learningMaterial2.id', ':lm')
                )
            );
            $qb->setParameter(':lm', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ?
                $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('m.courses', 'course');
            $qb->leftJoin('m.objectives', 'objective');
            $qb->leftJoin('m.sessions', 'session');
            $qb->leftJoin('m.courseLearningMaterials', 'clm');
            $qb->leftJoin('course.terms', 'terms');
            $qb->leftJoin('objective.courses', 'course2');
            $qb->leftJoin('course2.terms', 'terms2');
            $qb->leftJoin('clm.course', 'course3');
            $qb->leftJoin('course3.terms', 'terms3');
            $qb->leftJoin('session.course', 'course4');
            $qb->leftJoin('session.terms', 'terms4');
            $qb->leftJoin('course4.terms', 'terms5');
            $qb->leftJoin('objective.sessions', 'session2');
            $qb->leftJoin('session2.course', 'course5');
            $qb->leftJoin('session2.terms', 'terms6');
            $qb->leftJoin('course5.terms', 'terms7');
            $qb->leftJoin('m.sessionLearningMaterials', 'slm');
            $qb->leftJoin('slm.session', 'session3');
            $qb->leftJoin('session3.course', 'course6');
            $qb->leftJoin('session3.terms', 'terms8');
            $qb->leftJoin('course6.terms', 'terms9');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('terms.id', ':terms'),
                    $qb->expr()->in('terms2.id', ':terms'),
                    $qb->expr()->in('terms3.id', ':terms'),
                    $qb->expr()->in('terms4.id', ':terms'),
                    $qb->expr()->in('terms5.id', ':terms'),
                    $qb->expr()->in('terms6.id', ':terms'),
                    $qb->expr()->in('terms7.id', ':terms'),
                    $qb->expr()->in('terms8.id', ':terms'),
                    $qb->expr()->in('terms9.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['learningMaterials']);
        unset($criteria['terms']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("m.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }
        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('m.'.$sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}
