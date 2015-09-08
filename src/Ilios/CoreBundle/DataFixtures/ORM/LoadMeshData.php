<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture as DataFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Class LoadMeshData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshData extends DataFixture implements
  FixtureInterface,
  ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $fileToTypeMap = [
        'mesh_term' => 'MeshTerm',
        'mesh_concept' => 'MeshConcept',
        'mesh_descriptor' => 'MeshDescriptor',
        'mesh_semantic_type' => 'MeshSemanticType',
        'mesh_qualifier' => 'MeshQualifier',
        'mesh_tree_x_descriptor' => 'MeshTree',
        'mesh_previous_indexing' => 'MeshPreviousIndexing',
        'mesh_concept_x_semantic_type' => 'MeshConceptSemanticType',
        'mesh_concept_x_term' => 'MeshConceptTerm',
        'mesh_descriptor_x_qualifier' => 'MeshDescriptorQualifier',
        'mesh_descriptor_x_concept' => 'MeshDescriptorConcept',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // Ignore the entity manager that gets passed in.
        // Instead, grab the one we need from the DI container.
        $em = $this->container->get('ilioscore.meshdescriptor.manager');

        foreach ($this->fileToTypeMap as $file => $type) {
            $this->loadData($em, $file, $type);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param MeshDescriptorManagerInterface $manager
     * @param string $file
     * @param string $type
     */
    protected function loadData(MeshDescriptorManagerInterface $manager, $file, $type)
    {
        $path = $this->getDataFilePath($file . '.csv');

        $i = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                // step over the first row
                // since it contains the field names
                if (1 === $i) {
                    continue;
                }
                $manager->import($data, $type);
            }

            fclose($handle);
        }

    }

    /**
     * Finds and return the absolute path to a given data file.
     *
     * @param string $fileName name of the data file.
     * @return string the absolute path.
     */
    protected function getDataFilePath($fileName)
    {
        /**
         * @var FileLocator $fileLocator
         */
        $fileLocator = $this->container->get('file_locator');

        // TODO: pull this hardwired path to the data files out into configuration. [ST 2015/08/26]
        $path = $fileLocator->locate('@IliosCoreBundle/Resources/dataimport/' . basename($fileName));

        return $path;
    }
}