<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LearningMaterialInterface;
use App\Repository\LearningMaterialRepository;
use App\Service\TemporaryFileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Service\IliosFileSystem;

/**
 * Cleanup incorrectly stored mime types for learning materials.
 */
class FixLearningMaterialMimeTypesCommand extends Command
{
    public function __construct(
        protected IliosFileSystem $iliosFileSystem,
        private TemporaryFileSystem $temporaryFileSystem,
        protected LearningMaterialRepository $learningMaterialRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:fix-mime-types')
            ->setAliases(['ilios:maintenance:fix-mime-types'])
            ->setDescription('Cleanup incorrectly stored mime types for learning materials.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $totalLearningMaterialsCount = $this->learningMaterialRepository->getTotalLearningMaterialCount();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Ready to fix ' . $totalLearningMaterialsCount .
            ' learning materials. Shall we continue? </question>' . "\n",
            true
        );

        if ($helper->ask($input, $output, $question)) {
            $progress = new ProgressBar($output, $totalLearningMaterialsCount);
            $progress->setRedrawFrequency(208);
            $output->writeln("<info>Starting cleanup of learning materials...</info>");
            $progress->start();

            $fixed = 0;
            $skipped = 0;
            $offset = 0;
            $limit = 50;
            $errors = [];

            while ($fixed + $skipped < $totalLearningMaterialsCount) {
                /** @var LearningMaterialInterface[] $learningMaterials */
                $learningMaterials = $this->learningMaterialRepository->findBy([], ['id' => 'desc'], $limit, $offset);
                foreach ($learningMaterials as $lm) {
                    $mimetype = $lm->getMimetype();
                    if ($path = $lm->getRelativePath()) {
                        if (in_array($mimetype, ['link', 'citation'])) {
                            $contents = $this->iliosFileSystem->getFileContents($path);
                            $file = $this->temporaryFileSystem->createFile($contents);
                            if (false === $file) {
                                $errors[] = 'File not found for learning material # ' . $lm->getId();
                                $skipped++;
                            } else {
                                try {
                                    $newMimeType = $file->getMimeType();
                                } catch (\ErrorException) {
                                    $fileName = $lm->getFilename();
                                    $newMimeType = $this->getMimetypeForFileName($fileName);
                                }
                                $lm->setMimetype($newMimeType);
                                $this->learningMaterialRepository->update($lm, false);
                                $fixed++;
                            }
                        } else {
                            $skipped++;
                        }
                    } elseif (null !== $lm->getCitation()) {
                        if ($mimetype != 'citation') {
                            $lm->setMimetype('citation');
                            $this->learningMaterialRepository->update($lm, false);
                            $fixed++;
                        } else {
                            $skipped++;
                        }
                    } elseif (null !== $lm->getLink()) {
                        if ($mimetype != 'link') {
                            $lm->setMimetype('link');
                            $this->learningMaterialRepository->update($lm, false);
                            $fixed++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        $skipped++;
                    }
                    $progress->advance();
                }
                $this->learningMaterialRepository->flushAndClear();
                $offset += $limit;
            }

            $progress->finish();
            $output->writeln('');

            $output->writeln("<info>Updated mimetypes for {$fixed} learning materials successfully!</info>");
            if ($skipped) {
                $msg = "<comment>{$skipped} learning materials did not need to be fixed.</comment>";
                $output->writeln($msg);
            }
            if ($errors !== []) {
                foreach ($errors as $message) {
                    $output->writeln("<error>${message}</error>");
                }
            }

            return 0;
        } else {
            $output->writeln('<comment>Update canceled.</comment>');

            return 1;
        }
    }

    protected function getMimetypeForFileName($name)
    {
        //taken from https://stackoverflow.com/questions/35299457/getting-mime-type-from-file-name-in-php
        $typesByExtension = [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        ];

        $parts = explode('.', $name);
        $extension = array_pop($parts);
        if ($extension && array_key_exists($extension, $typesByExtension)) {
            return $typesByExtension[$extension];
        }

        return 'application/octet-stream';
    }
}
