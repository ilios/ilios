<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Doctrine event listener.
 * When a ProgramYear, Course, or Session is deleted cleanup any newly orphaned objectives.
 */
class RemoveOrphanedObjectives
{
    /**
     * @param OnFlushEventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof ProgramYearInterface ||
                $entity instanceof CourseInterface ||
                $entity instanceof SessionInterface
            ) {
                $objectives = $entity->getObjectives();
                foreach ($objectives as $objective) {
                    $programYears = $objective->getProgramYears();
                    $courses = $objective->getCourses();
                    $sessions = $objective->getSessions();
                    /** @var IdentifiableEntityInterface[] $allLinks */
                    $allLinks = $programYears->toArray() + $courses->toArray() + $sessions->toArray();
                    //ensure that this Objective is only linked to the deleted entity
                    if (count($allLinks) === 0 ||
                        (count($allLinks) === 1 && $allLinks[0]->getId() === $entity->getId())
                    ) {
                        $this->removeLinks($uow, $em, $objective);
                    }
                }
            }
        }
    }

    /**
     * @param UnitOfWork $uow
     * @param EntityManager $em
     * @param Objective $objective
     * @throws \Doctrine\ORM\ORMException
     */
    protected function removeLinks(UnitOfWork $uow, EntityManager $em, Objective $objective)
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
