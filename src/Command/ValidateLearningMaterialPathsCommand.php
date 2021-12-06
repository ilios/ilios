<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\LearningMaterialRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use App\Service\IliosFileSystem;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
class ValidateLearningMaterialPathsCommand extends Command
{

    public function __construct(
        protected IliosFileSystem $iliosFileSystem,
        protected LearningMaterialRepository $learningMaterialRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:validate-learning-materials')
            ->setAliases(['ilios:maintenance:validate-learning-materials'])
            ->setDescription('Validate file paths for learning materials');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $totalLearningMaterialsCount = $this->learningMaterialRepository->getTotalFileLearningMaterialCount();

        $progress = new ProgressBar($output, $totalLearningMaterialsCount);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting validate of learning materials...</info>");
        $progress->start();

        $valid = 0;
        $broken = [];
        $offset = 0;
        $limit = 500;

        while ($valid + count($broken) < $totalLearningMaterialsCount) {
            $learningMaterials = $this->learningMaterialRepository->findFileLearningMaterials($limit, $offset);
            foreach ($learningMaterials as $lm) {
                if ($this->iliosFileSystem->checkLearningMaterialFilePath($lm)) {
                    $valid++;
                } else {
                    $broken[] = [
                        'id' => $lm->getId(),
                        'path' => $lm->getRelativePath()
                    ];
                }
                $progress->advance();
            }
            $offset += $limit;
        }

        $progress->finish();
        $output->writeln('');

        $output->writeln("<info>Validated {$valid} learning materials file path.</info>");
        if ($broken !== []) {
            $msg = "<error>Unable to find the files for " . count($broken) . ' learning material.</error>';
            $output->writeln($msg);
            $rows = array_map(fn($arr) => [
                $arr['id'],
                $arr['path']
            ], $broken);
            $table = new Table($output);
            $table
                ->setHeaders(['Learning Material ID', 'Relative Path'])
                ->setRows($rows)
            ;
            $table->render();

            return 1;
        }

        return 0;
    }
}
