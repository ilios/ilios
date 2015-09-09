<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\Repository\MeshDescriptorRepository;

/**
 * Class MeshDescriptorManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshDescriptorManager extends AbstractManager implements MeshDescriptorManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshDescriptorInterface
     */
    public function findMeshDescriptorBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|MeshDescriptorInterface[]
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
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserInterface[]|Collection
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
     * @param MeshDescriptorInterface $meshDescriptor
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function deleteMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor
    ) {
        $this->em->remove($meshDescriptor);
        $this->em->flush();
    }

    /**
     * @return MeshDescriptorInterface
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
        $repository = $this->repository;
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
