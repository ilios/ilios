<?php

namespace Ilios\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class CurriculumInventorySequenceBlockSubscriber
 * @package Ilios\CoreBundle\EventListener
 */
class CurriculumInventorySequenceBlockSubscriber implements EventSubscriber
{
    /**
     * Reorders child sequence blocks if the child sequence order changes.
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (! $entity instanceof CurriculumInventorySequenceBlockInterface) {
            return;
        }
        /* @var CurriculumInventorySequenceBlockInterface[] $children */
        $children = $entity->getChildren()->toArray();
        if (empty($children)) {
            return;
        }

        $key = 'childSequenceOrder';
        if (! $eventArgs->hasChangedField($key)) {
            return;
        }
        $newValue = $eventArgs->getNewValue($key);
        $oldValue = $eventArgs->getOldValue($key);

        switch ($newValue) {
            case CurriculumInventorySequenceBlockInterface::ORDERED:
                usort($children, [CurriculumInventorySequenceBlock::class, 'compareSequenceBlocksWithOrderedStrategy']);
                for ($i = 0, $n = count($children); $i < $n; $i++) {
                    $children[$i]->setOrderInSequence($i + 1);
                }
                break;
            case CurriculumInventorySequenceBlockInterface::UNORDERED:
            case CurriculumInventorySequenceBlockInterface::PARALLEL:
                if ($oldValue === CurriculumInventorySequenceBlockInterface::ORDERED) {
                    for ($i = 0, $n = count($children); $i < $n; $i++) {
                        $children[$i]->setOrderInSequence(0);
                    }
                }
                break;
            default:
                // do nothing
        }
    }

    /**
     * Reorders siblings of the sequence block being deleted.
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (! $entity instanceof CurriculumInventorySequenceBlockInterface) {
            return;
        }
        $parent = $entity->getParent();
        if (! $parent || $parent->getChildSequenceOrder() !== CurriculumInventorySequenceBlockInterface::ORDERED) {
            return;
        }

        $siblings = $parent->getChildren()->toArray();
        /* @var CurriculumInventorySequenceBlockInterface[] $siblingsWithHigherSortOrder */
        $siblingsWithHigherSortOrder = array_filter($siblings, function($sibling) use ($entity) {
            /* @var CurriculumInventorySequenceBlockInterface $sibling */
            return ($sibling->getOrderInSequence() > $entity->getOrderInSequence());
        });
        for ($i = 0, $n = count($siblingsWithHigherSortOrder); $i < $n; $i++) {
            $orderInSequence = $siblingsWithHigherSortOrder[$i]->getOrderInSequence();
            $siblingsWithHigherSortOrder[$i]->setOrderInSequence($orderInSequence - 1);
        }
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::preRemove,
        ];
    }
}
