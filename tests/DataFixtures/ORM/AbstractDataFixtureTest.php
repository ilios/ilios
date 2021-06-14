<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Repository\RepositoryInterface;
use App\Service\DataimportFileLocator;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class for data loader tests.
 *
 * Class AbstractDataFixtureTest
 */
abstract class AbstractDataFixtureTest extends WebTestCase
{
    protected RepositoryInterface $em;
    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();

        /** @var RepositoryInterface $em */
        $em = $this->kernelBrowser->getContainer()->get($this->getEntityManagerServiceKey());
        $this->em = $em;
    }

    public function getFixtures()
    {
        return [];
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
    protected function runTestLoad(string $fileName, int $lineLimit = -1)
    {
        $container = $this->kernelBrowser->getContainer();
        $databaseTool = $container->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures($this->getFixtures());

        $dataFile = fopen($container->get(DataimportFileLocator::class)->getDataFilePath($fileName), 'r');

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
     * Returns the key of the entity repository service that needs to be loaded for this test.
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
