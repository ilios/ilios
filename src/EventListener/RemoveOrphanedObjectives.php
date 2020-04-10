<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\ObjectiveInterface;
use App\Entity\ObjectiveRelationshipInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\UnitOfWork;
use App\Entity\Objective;
use App\Traits\IdentifiableEntityInterface;

/**
 * Doctrine event listener.
 * When a ProgramYear-, Course-, or Session-Objective is deleted cleanup any newly orphaned objectives.
 */
class RemoveOrphanedObjectives
{
    /**
     * @param OnFlushEventArgs $eventArgs
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof ObjectiveRelationshipInterface) {
                /* @var ObjectiveInterface $objective */
                $objective = $entity->getObjective();
                $programYearObjectives = $objective->getProgramYearObjectives();
                $courseObjectives = $objective->getCourseObjectives();
                $sessionObjectives = $objective->getSessionObjectives();
                /** @var IdentifiableEntityInterface[] $allLinks */
                $allLinks = array_merge(
                    $programYearObjectives->toArray(),
                    $courseObjectives->toArray(),
                    $sessionObjectives->toArray()
                );
                //ensure that this Objective is only linked to the deleted entity
                if (
                    count($allLinks) === 0 ||
                    (count($allLinks) === 1 && $allLinks[0]->getId() === $entity->getId())
                ) {
                    $this->removeLinks($uow, $em, $objective);
                }
            }
        }
    }

    /**
     * @param UnitOfWork $uow
     * @param EntityManager $em
     * @param ObjectiveInterface $objective
     * @throws ORMException
     */
    protected function removeLinks(UnitOfWork $uow, EntityManager $em, ObjectiveInterface $objective)
    {
        $objective->setParents(new ArrayCollection());
        $objective->setCompetency(null);
        foreach ($objective->getChildren() as $child) {
            $child->removeParent($objective);
            $em->persist($child);
            $classMetadata = $em->getClassMetadata(Objective::class);
            $uow->computeChangeSet($classMetadata, $child);
        }
        $em->persist($objective);
        $classMetadata = $em->getClassMetadata(Objective::class);
        $uow->computeChangeSet($classMetadata, $objective);
    }
}
