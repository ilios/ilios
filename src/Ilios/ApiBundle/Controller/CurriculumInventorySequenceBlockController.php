<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CurriculumInventorySequenceBlockController
 * Sequence Blocks need to support some re-ordering when saved
 */
class CurriculumInventorySequenceBlockController extends ApiController
{
    /**
     * Re-order blocks when they are saved
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        /** @var CurriculumInventorySequenceBlockInterface $block */
        foreach ($entities as $block) {
            $this->reorderBlocksInSequenceOnOrderChange(
                0,
                $block
            );
            $manager->update($block, false);
        }


        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * Re-order blocks when they are saved
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id'=> $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }

        $oldChildSequenceOrder = $entity->getChildSequenceOrder();
        $oldOrderInSequence = $entity->getOrderInSequence();

        $json = $this->extractJsonFromRequest($request, $object, 'PUT');
        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $this->reorderChildrenOnChildSequenceOrderChange(
            $oldChildSequenceOrder,
            $entity
        );
        $this->reorderBlocksInSequenceOnOrderChange(
            $oldOrderInSequence,
            $entity
        );

        $manager->update($entity, false, false);
        $manager->flush();

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    /**
     * Re-order blocks when others are deleted
     * @inheritdoc
     */
    public function deleteAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        /** @var CurriculumInventorySequenceBlockInterface $entity */
        $entity = $manager->findOneBy(['id'=> $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $this->authorizationChecker->isGranted('delete', $entity)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->reorderSiblingsOnDeletion($entity);
            $manager->delete($entity);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Reorders siblings of the sequence block being deleted.
     * @param CurriculumInventorySequenceBlockInterface $block
     */
    protected function reorderSiblingsOnDeletion(
        CurriculumInventorySequenceBlockInterface $block
    ) {
        $manager = $this->getManager('curriculuminventorysequences');
        $parent = $block->getParent();
        if (! $parent || $parent->getChildSequenceOrder() !== CurriculumInventorySequenceBlockInterface::ORDERED) {
            return;
        }

        $siblings = $parent->getChildren()->toArray();
        /* @var CurriculumInventorySequenceBlockInterface[] $siblingsWithHigherSortOrder */
        $siblingsWithHigherSortOrder = array_values(array_filter($siblings, function ($sibling) use ($block) {
            /* @var CurriculumInventorySequenceBlockInterface $sibling */
            return ($sibling->getOrderInSequence() > $block->getOrderInSequence());
        }));
        for ($i = 0, $n = count($siblingsWithHigherSortOrder); $i < $n; $i++) {
            $orderInSequence = $siblingsWithHigherSortOrder[$i]->getOrderInSequence();
            $siblingsWithHigherSortOrder[$i]->setOrderInSequence($orderInSequence - 1);
            $manager->update($block, false, false);
        }
    }

    /**
     * Reorders child sequence blocks if the parent's child sequence order changes.
     * @param int $oldValue
     * @param CurriculumInventorySequenceBlockInterface $block
     * @internal param ManagerInterface $manager
     */
    protected function reorderChildrenOnChildSequenceOrderChange(
        $oldValue,
        CurriculumInventorySequenceBlockInterface $block
    ) {
        $manager = $this->getManager('curriculuminventorysequences');

        /* @var CurriculumInventorySequenceBlockInterface[] $children */
        $children = $block->getChildren()->toArray();
        if (empty($children)) {
            return;
        }

        $newValue = $block->getChildSequenceOrder();

        if ($newValue === $oldValue) {
            return;
        }

        switch ($newValue) {
            case CurriculumInventorySequenceBlockInterface::ORDERED:
                usort($children, [CurriculumInventorySequenceBlock::class, 'compareSequenceBlocksWithDefaultStrategy']);
                for ($i = 0, $n = count($children); $i < $n; $i++) {
                    $children[$i]->setOrderInSequence($i + 1);
                    $manager->update($children[$i], false);
                }
                break;
            case CurriculumInventorySequenceBlockInterface::UNORDERED:
            case CurriculumInventorySequenceBlockInterface::PARALLEL:
                if ($oldValue === CurriculumInventorySequenceBlockInterface::ORDERED) {
                    for ($i = 0, $n = count($children); $i < $n; $i++) {
                        $children[$i]->setOrderInSequence(0);
                        $manager->update($children[$i], false);
                    }
                }
                break;
            default:
                // do nothing
        }
    }

    /**
     * Reorder the entire sequence if one of the blocks changes position.
     * @param int $oldValue
     * @param CurriculumInventorySequenceBlockInterface $block
     * @throws \OutOfRangeException
     */
    protected function reorderBlocksInSequenceOnOrderChange(
        $oldValue,
        CurriculumInventorySequenceBlockInterface $block
    ) {
        $manager = $this->getManager('curriculuminventorysequences');

        $parent = $block->getParent();
        if (! $parent) {
            return;
        }
        if ($parent->getChildSequenceOrder() !== CurriculumInventorySequenceBlockInterface::ORDERED) {
            return;
        }

        $newValue = $block->getOrderInSequence();

        $blocks = $parent->getChildrenAsSortedList();

        $blocks = array_filter($blocks, function (CurriculumInventorySequenceBlockInterface $sibling) use ($block) {
            return $sibling->getId() !== $block->getId();
        });
        $blocks = array_values($blocks);

        $minRange = 1;
        $maxRange = count($blocks) + 1;
        if ($newValue < $minRange || $newValue > $maxRange) {
            throw new \OutOfRangeException(
                "The given order-in-sequence value {$newValue} falls outside the range {$minRange} - {$maxRange}."
            );
        }

        if ($oldValue === $newValue) {
            return;
        }

        array_splice($blocks, $block->getOrderInSequence() - 1, 0, [$block]);
        for ($i = 0, $n = count($blocks); $i < $n; $i++) {
            /* @var CurriculumInventorySequenceBlockInterface $current */
            $current = $blocks[$i];
            $j = $i + 1;
            if ($current->getId() !== $block && $current->getOrderInSequence() !== $j) {
                $current->setOrderInSequence($j);
                $manager->update($current, false, false);
            }
        }
    }
}
