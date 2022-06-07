<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DefaultDataImporter;
use App\Service\DTOCacheTagger;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\CurriculumInventoryInstitution;
use App\Entity\DTO\CurriculumInventoryInstitutionDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

use function array_keys;

class CurriculumInventoryInstitutionRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, CurriculumInventoryInstitution::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventoryInstitution::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CurriculumInventoryInstitutionDTO(
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

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, school.id AS schoolId'
            )
            ->from(CurriculumInventoryInstitution::class, 'x')
            ->join('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        return array_values($dtos);
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `school_id`,`name`,`aamc_code`,`address_street`,`address_city`,
        // `address_state_or_province`,`address_zipcode`,
        // `address_country_code`,`institution_id`
        $entity = new CurriculumInventoryInstitution();
        $entity->setSchool($referenceMap[DefaultDataImporter::SCHOOL . $data[0]]);
        $entity->setName($data[1]);
        $entity->setAamcCode($data[2]);
        $entity->setAddressStreet($data[3]);
        $entity->setAddressCity($data[4]);
        $entity->setAddressStateOrProvince($data[5]);
        $entity->setAddressZipCode($data[6]);
        $entity->setAddressCountryCode($data[7]);
        $entity->setId($data[8]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
