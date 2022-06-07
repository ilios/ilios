<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\MeshTree;
use App\Entity\DTO\MeshTreeDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

use function array_keys;

class MeshTreeRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, MeshTree::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(MeshTree::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new MeshTreeDTO(
                $arr['id'],
                $arr['treeNumber']
            );
        }

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, descriptor.id AS descriptorId'
            )
            ->from('App\Entity\MeshTree', 'x')
            ->join('x.descriptor', 'descriptor')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->descriptor = (string) $arr['descriptorId'];
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
}
