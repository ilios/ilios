<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\DataFixtures\AbstractFixture as DataFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Doctrine fixture based data loader base class that populates the DB from data files.
 *
 * Class AbstractFixture
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
abstract class AbstractFixture extends DataFixture implements
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var string doubles as identifier for this fixture's data file and entity references.
     */
    protected $key;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {

        $fileName = $this->getKey() . '.csv';
        $path = $this->getDataFilePath($fileName);

        $first = true;
        if (($handle = fopen($path, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                // step over the first row
                // since it contains the field names
                if ($first) {
                    $first = false;
                    continue;
                }
                $entity = $this->createEntity($data);
                $manager->persist($entity);

                // honor the given entity identifiers.
                // @link http://www.ens.ro/2012/07/03/symfony2-doctrine-force-entity-id-on-persist/
                $metadata = $manager->getClassMetaData(get_class($entity));
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

                $this->addReference($this->getKey() . $entity->getId(), $entity);

                $manager->flush();
            }
            fclose($handle);
        }
    }

    /**
     * Finds and return the absolute path to a given data file.
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

    /**
     * Instantiates and returns an entity and populates it with data from a given array.
     * Note that data persistence is not in scope for this method.
     *
     * @param array $data
     * @return IdentifiableEntityInterface
     */
    protected abstract function createEntity(array $data);
}
