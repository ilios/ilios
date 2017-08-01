<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryInstitutionDTO;

/**
 * Class CurriculumInventoryInstitutionRepository
 */
class CurriculumInventoryInstitutionRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:CurriculumInventoryInstitution', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('IliosCoreBundle:CurriculumInventoryInstitution', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var CurriculumInventoryInstitutionDTO[] $curriculumInventoryInstitutionDTOs */
        $curriculumInventoryInstitutionDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $curriculumInventoryInstitutionDTOs[$arr['id']] = new CurriculumInventoryInstitutionDTO(
                $arr['id'],
                $arr['name'],
                $arr['aamcCode'],
                $arr['addressStreet'],
                $arr['addressCity'],
                $arr['addressStateOrProvince'],
                $arr['addressZipCode'],
                $arr['addressCountryCode']
            );
        }

        $curriculumInventoryInstitutionIds = array_keys($curriculumInventoryInstitutionDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, school.id AS schoolId'
            )
            ->from('IliosCoreBundle:CurriculumInventoryInstitution', 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryInstitutionIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $curriculumInventoryInstitutionDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        return array_values($curriculumInventoryInstitutionDTOs);
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
        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.'.$sort, $order);
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
