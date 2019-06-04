<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\Manager\ManagerInterface;

use App\Service\DataimportFileLocator;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

/**
 * Base class for data loader tests.
 *
 * Class AbstractDataFixtureTest
 */
abstract class AbstractDataFixtureTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var ManagerInterface
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->loadEntityManager($this->getEntityManagerServiceKey());
    }

    /**
     * @param string $serviceKey
     */
    protected function loadEntityManager($serviceKey)
    {
        $this->em = $this->getContainer()->get($serviceKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * Executes the test.
     * Call this from loadTest() implementations in child classes.
     *
     * @param string $fileName name of the data file to load and import.
     * @param int $lineLimit number of lines to process. Set to -1 to process all lines in data file.
     *
     * @see AbstractDataFixtureTest::loadTest()
     */
    protected function runTestLoad($fileName, $lineLimit = -1)
    {
        $this->loadFixtures($this->getFixtures());

        $dataFile = fopen($this->getContainer()->get(DataimportFileLocator::class)->getDataFilePath($fileName), 'r');

        $i = 0;
        while (($data = fgetcsv($dataFile)) !== false && ($lineLimit < 0 || $lineLimit >= $i)) {
            $i++;
            // step over the first row
            // since it contains the field names
            if (1 === $i) {
                continue;
            }
            $entity = $this->getEntity($data);
            $this->assertDataEquals($data, $entity);
        }

        fclose($dataFile);
    }

    /**
     * Returns the key of the entity manager service that needs to be loaded for this test.
     *
     * @return string
     */
    abstract public function getEntityManagerServiceKey();

    /**
     * Implement this method to provide coverage for a data loader's load() method.
     */
    abstract public function testLoad();

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
    protected function getEntity(array $data)
    {
        return $this->em->findOneBy(['id' => $data[0]]);
    }
}
