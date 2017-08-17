<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Symfony\Component\Console\Helper\ProgressBar;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Service\IliosFileSystem;

/**
 * Migrate Learning materials from Ilios2 location to Ilios3 location
 *
 * Class MigrateIlios2LearningMaterialsCommand
 */
class MigrateIlios2LearningMaterialsCommand extends Command
{
    /**
     * @var SymfonyFileSystem
     */
    protected $symfonyFileSystem;
    
    /**
     * @var IliosFileSystem
     */
    protected $iliosFileSystem;
    
    /**
     * @var LearningMaterialManager
     */
    protected $learningMaterialManager;
    
    public function __construct(
        SymfonyFileSystem $symfonyFileSystem,
        IliosFileSystem $iliosFileSystem,
        LearningMaterialManager $learningMaterialManager
    ) {
        $this->symfonyFileSystem = $symfonyFileSystem;
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
            ->setName('ilios:setup:migrate-learning-materials')
            ->setDescription('Migrate Ilios2 Learning Materials to Ilios3 Structure')
            ->addArgument(
                'pathToIlios2',
                InputArgument::REQUIRED,
                'The path to your Ilios2 installation.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToIlios2 = $input->getArgument('pathToIlios2');
        if (!$this->symfonyFileSystem->exists($pathToIlios2)) {
            throw new \Exception(
                "'{$pathToIlios2}' does not exist."
            );
        }
        
        $totalLearningMaterialsCount = $this->learningMaterialManager->getTotalFileLearningMaterialCount();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Ready to copy ' . $totalLearningMaterialsCount .
            ' learning materials. Shall we continue? </question>' . "\n",
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            $progress = new ProgressBar($output, $totalLearningMaterialsCount);
            $progress->setRedrawFrequency(208);
            $output->writeln("<info>Starting migration of learning materials...</info>");
            $progress->start();

            $migrated = 0;
            $skipped = 0;
            $offset = 0;
            $limit = 50;

            while ($migrated + $skipped < $totalLearningMaterialsCount) {
                $learningMaterials = $this->learningMaterialManager->findFileLearningMaterials($limit, $offset);
                foreach ($learningMaterials as $lm) {
                    $fullPath = $pathToIlios2 . $lm->getRelativePath();
                    if (!$this->symfonyFileSystem->exists($fullPath)) {
                        $skipped++;
                    } else {
                        $file = $this->iliosFileSystem->getSymfonyFileForPath($fullPath);
                        $newPath = $this->iliosFileSystem->storeLearningMaterialFile($file);
                        $lm->setRelativePath($newPath);
                        $this->learningMaterialManager->update($lm, false);
                        $migrated ++;
                    }
                    $progress->advance();
                }
                $this->learningMaterialManager->flushAndClear();
                $offset += $limit;
            }

            $progress->finish();
            $output->writeln('');
            
            $output->writeln("<info>Migrated {$migrated} learning materials successfully!</info>");
            if ($skipped) {
                $msg = "<comment>Skipped {$skipped} learning materials because they could not be located " .
                "or were already migrated.</comment>";
                $output->writeln($msg);
            }
        } else {
            $output->writeln('<comment>Migration canceled.</comment>');
        }
    }
}
