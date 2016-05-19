<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\Repository\MeshDescriptorRepository;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class MeshDescriptorManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshDescriptorManager extends BaseManager implements MeshDescriptorManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMeshDescriptorBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshDescriptorDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);

        return empty($results)?false:$results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshDescriptorsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshDescriptorDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshDescriptorsByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshDescriptor);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshDescriptor));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor
    ) {
        $this->em->remove($meshDescriptor);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createMeshDescriptor()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function import(array $data, $type)
    {
        // KLUDGE!
        // For performance reasons, we're completely side-stepping
        // Doctrine's entity layer.
        // Instead, this method invokes low-level/native-SQL import-methods
        // on this manager's repository.
        // [ST 2015/09/08]
        /**
         * @var MeshDescriptorRepository $repository
         */
        $repository = $this->getRepository();
        switch ($type) {
            case 'MeshDescriptor':
                $repository->importMeshDescriptor($data);
                break;
            case 'MeshTree':
                $repository->importMeshTree($data);
                break;
            case 'MeshConcept':
                $repository->importMeshConcept($data);
                break;
            case 'MeshSemanticType':
                $repository->importMeshSemanticType($data);
                break;
            case 'MeshTerm':
                $repository->importMeshTerm($data);
                break;
            case 'MeshQualifier':
                $repository->importMeshQualifier($data);
                break;
            case 'MeshPreviousIndexing':
                $repository->importMeshPreviousIndexing($data);
                break;
            case 'MeshConceptSemanticType':
                $repository->importMeshConceptSemanticType($data);
                break;
            case 'MeshConceptTerm':
                $repository->importMeshConceptTerm($data);
                break;
            case 'MeshDescriptorQualifier':
                $repository->importMeshDescriptorQualifier($data);
                break;
            case 'MeshDescriptorConcept':
                $repository->importMeshDescriptorConcept($data);
                break;
            default:
                throw new \Exception("Unsupported type ${type}.");
        }
    }
}
