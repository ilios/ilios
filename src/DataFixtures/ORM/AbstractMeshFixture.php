<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Repository\MeshDescriptorRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture as DataFixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\DataimportFileLocator;

/**
 * A data-loader base-class for importing MeSH records from data files.
 *
 * Class AbstractMeshFixture
 */
abstract class AbstractMeshFixture extends DataFixture implements ORMFixtureInterface
{
    /**
     * @var string The name of the data file to import.
     */
    protected $filename;

    /**
     * @var string The type of MeSH data to import.
     */
    protected $type;

    /**
     * @var MeshDescriptorRepository
     */
    private $meshDescriptorRepository;

    /**
     * @var DataimportFileLocator
     */
    private $dataimportFileLocator;

    /**
     * @param string $filename The name of the data file to import.
     * @param string $type The type of MeSH data to import.
     */
    public function __construct(
        MeshDescriptorRepository $meshDescriptorRepository,
        DataimportFileLocator $dataimportFileLocator,
        $filename,
        $type
    ) {
        $this->meshDescriptorRepository = $meshDescriptorRepository;
        $this->dataimportFileLocator = $dataimportFileLocator;
        $this->filename = $filename;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $path = $this->dataimportFileLocator->getDataFilePath($this->filename);
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $i = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                // step over the first row
                // since it contains the field names
                if (1 === $i) {
                    continue;
                }
                $this->meshDescriptorRepository->import($data, $this->type, $now);
            }

            // clean-up
            fclose($handle);
        }

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
}
