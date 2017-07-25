<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture as DataFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\CoreBundle\Service\DataimportFileLocator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A data-loader base-class for importing MeSH records from data files.
 *
 * Class AbstractMeshFixture
 */
abstract class AbstractMeshFixture extends DataFixture implements
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string The name of the data file to import.
     */
    protected $filename;

    /**
     * @var string The type of MeSH data to import.
     */
    protected $type;

    /**
     * @param string $filename The name of the data file to import.
     * @param string $type The type of MeSH data to import.
     */
    public function __construct($filename, $type)
    {
        $this->filename = $filename;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // Ignore the entity manager that gets passed in.
        // Instead, grab the one we need from the DI container.
        $em = $this->container->get(MeshDescriptorManager::class);

        $path = $this->container->get(DataimportFileLocator::class)->getDataFilePath($this->filename);

        $i = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                // step over the first row
                // since it contains the field names
                if (1 === $i) {
                    continue;
                }
                $em->import($data, $this->type);
            }

            // clean-up
            fclose($handle);
        }

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
