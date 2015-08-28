<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\ManagerInterface;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for data loader tests.
 *
 * Class AbstractDataFixtureTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
abstract class AbstractDataFixtureTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var resource
     */
    protected $dataFile;

    /**
     * @var ManagerInterface
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = static::createClient()->getContainer();
        $this->loadFixtures($this->getFixtures());
        $this->loadDataFile($this->getDataFileName());
        $this->loadEntityManager($this->getEntityManagerServiceKey());
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(){
        fclose($this->dataFile);
    }

    /**
     * @param string $fileName
     */
    protected function loadDataFile($fileName)
    {
        /**
         * @var FileLocator $fileLocator
         */
        $fileLocator = $this->container->get('file_locator');
        $path = $fileLocator->locate('@IliosCoreBundle/Resources/dataimport/' . basename($fileName));
        $this->dataFile = fopen($path, 'r');
    }

    /**
     * @param string $serviceKey
     */
    protected function loadEntityManager($serviceKey)
    {
        $this->em = $this->container->get($serviceKey);
    }



    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\AbstractFixture::load
     */
    public function testLoad()
    {
        $first = true;
        while (($data = fgetcsv($this->dataFile)) !== FALSE) {
            // step over the first row
            // since it contains the field names
            if ($first) {
                $first = false;
                continue;
            }
            $entity = $this->getEntity($data);
            $this->assertDataEquals($data, $entity);
        }
    }

    /**
     * Returns the base name of the data file that was loaded by the data loader under test.
     *
     * @return string
     */
    abstract public function getDataFileName();

    /**
     * Returns the key of the entity manager service that needs to be loaded for this test.
     *
     * @return string
     */
    abstract public function getEntityManagerServiceKey();

    /**
     * Asserts data equality of a given array and entity.
     *
     * @param array $data
     * @param mixed $entity
     */
    abstract protected function assertDataEquals(array $data, $entity);

    /**
     * Retrieves an the corresponding entity for a given data array.
     *
     * @param array $data
     * @return mixed the entity
     */
    abstract protected function getEntity(array $data);
}