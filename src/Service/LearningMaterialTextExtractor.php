<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DTO\LearningMaterialDTO;
use App\Exception\LearningMaterialTextExtractorException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Vaites\ApacheTika\Client;
use Normalizer;

class LearningMaterialTextExtractor
{
    private Client $client;

    public function __construct(
        protected NonCachingIliosFileSystem $fileSystem,
        protected TemporaryFileSystem $temporaryFileSystem,
        protected IliosFileSystem $iliosFileSystem,
        ?Client $client = null,
    ) {
        if ($client) {
            $this->client = $client;
        }
    }

    public function extract(LearningMaterialDTO $dto): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (!$dto->filename) {
            //not a File LM
            return;
        }

        if (!$this->client->isMIMETypeSupported($dto->mimetype)) {
            //not the type of file tika can extract
            return;
        }

        if ($this->iliosFileSystem->checkIfLearningMaterialTextFileExists($dto->relativePath)) {
            //this LM has already been extracted
            return;
        }

        if (!$this->fileSystem->checkLearningMaterialRelativePath($dto->relativePath)) {
            throw new LearningMaterialTextExtractorException(
                "There is no material on this system at $dto->relativePath"
            );
        }

        $contents = $this->fileSystem->getFileContents($dto->relativePath);
        $tmpFile = $this->temporaryFileSystem->createFile($contents);

        try {
            $raw = $this->client->getText($tmpFile->getRealPath());
            $text = $this->cleanText($raw);
            $this->iliosFileSystem->storeLearningMaterialText($dto->relativePath, $text);
        } catch (Exception $exception) {
            if (
                // error code from php-tika library (422)
                // https://github.com/vaites/php-apache-tika/blob/792dc1254b4ccd92c6964ab477a1626dca6784ee/src/Clients/WebClient.php#L620
                $exception->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY &&
                $exception->getMessage() === 'Unprocessable document'
            ) {
                //this document can't be processed by tika
                //we don't want to keep trying, so we store the filename as the text
                $this->iliosFileSystem->storeLearningMaterialText($dto->relativePath, $dto->filename);
            }
        } finally {
            if (file_exists($tmpFile->getRealPath())) {
                unlink($tmpFile->getRealPath());
            }
        }
    }

    private function isEnabled(): bool
    {
        return !empty($this->client);
    }

    /**
     * Attempt to remove the garbage from the text returned by Tika.
     * Each step gets a little bit closer
     */
    private function cleanText(string $text): string
    {
        if (class_exists(Normalizer::class)) {
            //clean up the text a bit making it easier to parse later
            $text = Normalizer::normalize($text, Normalizer::FORM_C);
        }

        //split into lines, it's easier to work with each line and filter it in or out
        $arr = preg_split('/\r\n|\r|\n/', $text);

        //remove any lines that container a single word with 100 or more characters
        //as it is probably binary data
        $arr = preg_grep('/\b\w{101,}\b/', $arr, PREG_GREP_INVERT);

        //convert it to JSON because we can sub invalid UTF-8 characters
        $json = json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);

        //Remove the replaced invalid UTF-8 characters
        $text = str_replace("\\ufffd", "", $json);

        //back to an array, that is cleaner now and can be filtered some more
        $arr = json_decode($text, true);

        //keep lines that have a letter, number, or space in them
        $arr = preg_grep('/[a-z0-9\s]+/', $arr);

        //remove any lines that contain *only* quotes, exclamation points, or double quotes
        $arr = preg_grep('/[\'"!]+/', $arr, PREG_GREP_INVERT);

        //remove any lines that only have whitespace
        $arr = array_filter($arr, fn($v) => trim($v) !== '');

        return implode("\n", $arr);
    }
}
