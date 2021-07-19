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
    public function __construct(
        private MeshDescriptorRepository $meshDescriptorRepository,
        private DataimportFileLocator $dataimportFileLocator,
        protected string $filename,
        protected string $type
    ) {
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
