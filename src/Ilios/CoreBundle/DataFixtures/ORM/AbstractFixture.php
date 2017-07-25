<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\DataFixtures\AbstractFixture as DataFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Ilios\CoreBundle\Service\DataimportFileLocator;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A generic data-loader base-class for importing entities from data files.
 *
 * Class AbstractFixture
 *
 * @link http://docs.doctrine-project.org/en/latest/reference/batch-processing.html#bulk-inserts
 */
abstract class AbstractFixture extends DataFixture implements
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var int number of insert statements per batch.
     */
    const BATCH_SIZE = 200;
    /**
     * @var string
     * Doubles as identifier for this fixture's data file and entity references.
     */
    protected $key;

    /**
     * @var boolean
     * Set to TRUE if the loaded fixture should be held on for reference.
     */
    protected $storeReference;

    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @param string $key
     * @param boolean $storeReference
     */
    public function __construct($key, $storeReference = true)
    {
        $this->key = $key;
        $this->storeReference = $storeReference;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // disable the SQL logger
        // @link http://stackoverflow.com/a/30924545
        $manager->getConnection()->getConfiguration()->setSQLLogger(null);

        $fileName = $this->getKey() . '.csv';
        $path = $this->container->get(DataimportFileLocator::class)->getDataFilePath($fileName);

        // honor the given entity identifiers.
        // @link http://www.ens.ro/2012/07/03/symfony2-doctrine-force-entity-id-on-persist/
        $manager
          ->getClassMetaData(get_class($this->createEntity()))
          ->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $i = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                // step over the first row
                // since it contains the field names
                if (1 === $i) {
                    continue;
                }

                $entity = $this->populateEntity($this->createEntity(), $data);
                $manager->persist($entity);

                if (($i % self::BATCH_SIZE) === 0) {
                    $manager->flush();
                    $manager->clear();
                }

                if ($this->storeReference) {
                    $this->addReference(
                        $this->getKey() . $entity->getId(),
                        $entity
                    );
                }
            }

            $manager->flush();
            $manager->clear();

            fclose($handle);
        }

        // Force PHP's GC
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

    /**
     * Instantiates and returns the primary target entity of this fixture.
     *
     * @return mixed
     */
    abstract protected function createEntity();

    /**
     * Populates a given entity with the data contained in a given array,
     * then returns the populated entity.
     * Note that data persistence is not in scope for this method.
     *
     * @param mixed $entity
     * @param array $data
     * @return IdentifiableEntityInterface
     */
    abstract protected function populateEntity($entity, array $data);
}
