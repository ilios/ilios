<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Ilios\CoreBundle\Classes\UserEvent;

class UserRepository extends EntityRepository
{
    /**
     * Find by a string query
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param offset $offset
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'u')->from('IliosCoreBundle:User', 'u');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return new ArrayCollection([]);
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.firstName', "?{$key}"),
                $qb->expr()->like('u.lastName', "?{$key}"),
                $qb->expr()->like('u.middleName', "?{$key}"),
                $qb->expr()->like('u.email', "?{$key}")
            ))
            ->setParameter($key, '%' . $term . '%');
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('u.' . $sort, $order);
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
     * @param integer $userId
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return UserEvent[]|Collection
     */
    public function findEventsForUser(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {

        $events = [];
        $qb = $this->_em->createQueryBuilder();
        $what = 'o.id, o.startDate, o.endDate, o.room, o.updatedAt, ' .
          's.title, s.publishedAsTbd, st.sessionTypeCssClass, pe.id as publishEventId';
        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.learnerGroups', 'lg');
        $qb->leftJoin('lg.offerings', 'o');
        $qb->leftJoin('o.session', 's');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.publishEvent', 'pe');

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('u.id', ':user_id'),
            $qb->expr()->between('o.startDate', ':date_from', ':date_to'),
            $qb->expr()->eq('o.deleted', 0)
        ));
        $qb->setParameter('user_id', $id);

        $qb->setParameter('date_from', $from, DoctrineType::DATETIME);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME);

        $results = $qb->getQuery()->getArrayResult();

        $events = array_map(function ($arr) use ($id) {
            $event = new UserEvent;
            $event->user = $id;
            $event->name = $arr['title'];
            $event->startDate = $arr['startDate'];
            $event->endDate = $arr['endDate'];
            $event->offering = $arr['id'];
            $event->location = $arr['room'];
            $event->eventClass = $arr['sessionTypeCssClass'];
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = !empty($arr['publishEventId']);
            $event->isScheduled = $arr['publishedAsTbd'];

            return $event;
        }, $results);

        return $events;
    }
}
