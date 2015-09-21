<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialManagerInterface;
use Ilios\CoreBundle\Classes\IliosFileSystem;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 * @package Ilios\CliBUndle\Command
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
     * @var LearningMaterialManagerInterface
     */
    protected $learningMaterialManager;
    
    public function __construct(
        SymfonyFileSystem $symfonyFileSystem,
        IliosFileSystem $iliosFileSystem,
        LearningMaterialManagerInterface $learningMaterialManager
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
        
        $learningMaterials = $this->learningMaterialManager->findFileLearningMaterials();
        
        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Ready to copy ' . count($learningMaterials) .
            ' learning materials. Shall we continue? </question>' . "\n",
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            $migrated = 0;
            foreach ($learningMaterials as $lm) {
                $fullPath = $pathToIlios2 . $lm->getRelativePath();
                if (!$this->symfonyFileSystem->exists($fullPath)) {
                    throw new \Exception(
                        'Unable to migrated learning material #' . $lm ->getId() .
                        ".  No file found at '${fullPath}'."
                    );
                }
                $file = $this->iliosFileSystem->getSymfonyFileForPath($fullPath);
                $newPath = $this->iliosFileSystem->storeLearningMaterialFile($file);
                $lm->setRelativePath($newPath);
                $this->learningMaterialManager->updateLearningMaterial($lm, false);
                $migrated ++;

                if ($migrated % 500) {
                    $this->learningMaterialManager->flushAndClear();
                }
            }
            
            $this->learningMaterialManager->flushAndClear();
            
            $output->writeln("<info>Migrated {$migrated} learning materials successfully!</info>");
        } else {
            $output->writeln('<comment>Migration canceled.</comment>');
        }
        
    }
}
