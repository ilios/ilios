<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\ORM\Mapping\ClassMetadata;

trait ImportableEntityRepository
{
    protected function importEntity(object $entity): void
    {
        $em = $this->getEntityManager();
        $metadata = $em->getClassMetadata($entity::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $em->persist($entity);
        $em->flush();
    }
}
