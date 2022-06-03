<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Program;
use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\ProgramDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

use function array_keys;
use function array_values;

class ProgramRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected TagAwareCacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, Program::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(Program::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $programDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $programDTOs[$arr['id']] = new ProgramDTO(
                $arr['id'],
                $arr['title'],
                $arr['shortTitle'],
                $arr['duration']
            );
        }
        return $this->attachAssociationsToDTOs($programDTOs);
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.programYears', 'c_programYear');
            $qb->join('c_programYear.cohort', 'c_cohort');
            $qb->join('c_cohort.courses', 'c_course');
            $qb->andWhere($qb->expr()->in('c_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('x.programYears', 'se_programYear');
            $qb->join('se_programYear.cohort', 'se_cohort');
            $qb->join('se_cohort.courses', 'se_course');
            $qb->join('se_course.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('x.programYears', 't_programYear');
            $qb->join('t_programYear.terms', 't_term');
            $qb->andWhere($qb->expr()->in('t_term.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['terms']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    protected function attachAssociationsToDTOs(array $dtos): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p.id as programId, s.id as schoolId')
            ->from(Program::class, 'p')
            ->join('p.school', 's')
            ->where($qb->expr()->in('p.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));
        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['programId']]->school = (int) $arr['schoolId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'programYears',
                'curriculumInventoryReports',
                'directors',
            ],
        );

        return array_values($dtos);
    }
}
