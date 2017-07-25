<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Service\IliosFileSystem;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
class ValidateLearningMaterialPathsCommand extends Command
{
    
    /**
     * @var IliosFileSystem
     */
    protected $iliosFileSystem;
    
    /**
     * @var LearningMaterialManager
     */
    protected $learningMaterialManager;
    
    public function __construct(
        IliosFileSystem $iliosFileSystem,
        LearningMaterialManager $learningMaterialManager
    ) {
        $this->iliosFileSystem = $iliosFileSystem;
        $this->learningMaterialManager = $learningMaterialManager;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:validate-learning-materials')
            ->setDescription('Validate file paths for learning materials');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $totalLearningMaterialsCount = $this->learningMaterialManager->getTotalFileLearningMaterialCount();

        $progress = new ProgressBar($output, $totalLearningMaterialsCount);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting validate of learning materials...</info>");
        $progress->start();

        $valid = 0;
        $broken = [];
        $offset = 0;
        $limit = 500;

        while ($valid + count($broken) < $totalLearningMaterialsCount) {
            $learningMaterials = $this->learningMaterialManager->findFileLearningMaterials($limit, $offset);
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
        if (count($broken)) {
            $msg = "<error>Unable to find the files for " . count($broken) . ' learning material.</error>';
            $output->writeln($msg);
            $rows = array_map(function ($arr) {
                return [
                    $arr['id'],
                    $arr['path']
                ];
            }, $broken);
            $table = new Table($output);
            $table
                ->setHeaders(array('Learning Material ID', 'Relative Path'))
                ->setRows($rows)
            ;
            $table->render();
        }
    }
}
