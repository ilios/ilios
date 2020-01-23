<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\LearningMaterialDTO;
use App\Service\Config;
use App\Service\NonCachingIliosFileSystem;
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\FpdiException;
use setasign\Fpdi\PdfParser\StreamReader;
use InvalidArgumentException;
use SplFileInfo;

class LearningMaterials extends ElasticSearchBase
{
    public const INDEX = 'ilios-learning-materials';

    /**
     * @var NonCachingIliosFileSystem
     */
    private $nonCachingIliosFileSystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        NonCachingIliosFileSystem $nonCachingIliosFileSystem,
        Config $config,
        LoggerInterface $logger,
        Client $client = null
    ) {
        parent::__construct($config, $client);
        $this->nonCachingIliosFileSystem = $nonCachingIliosFileSystem;
        $this->logger = $logger;
    }

    /**
     * @param LearningMaterialDTO[] $materials
     * @return bool
     */
    public function index(array $materials): bool
    {
        foreach ($materials as $material) {
            if (!$material instanceof LearningMaterialDTO) {
                throw new InvalidArgumentException(
                    sprintf(
                        '$materials must be an array of %s. %s found',
                        LearningMaterialDTO::class,
                        get_class($material)
                    )
                );
            }
        }

        $extractedMaterials = array_reduce($materials, function ($materials, LearningMaterialDTO $lm) {
            foreach ($this->extractLearningMaterialData($lm) as $data) {
                $materials[] = [
                    'id' => $lm->id,
                    'data' => $data
                ];
            }
            return $materials;
        }, []);

        $results = array_map(function (array $arr) {
            $params = [
                'index' => self::INDEX,
                'type' => '_doc',
                'pipeline' => 'learning_materials',
                'body' => [
                    'learningMaterialId' => $arr['id'],
                    'data' => $arr['data'],
                ]
            ];
            return $this->client->index($params);
        }, $extractedMaterials);

        $errors = array_filter($results, function ($result) {
            return $result['result'] === 'error';
        });

        return empty($errors);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $result = $this->doDeleteByQuery([
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'term' => ['learningMaterialId' => $id]
                ]
            ]
        ]);

        return !count($result['failures']);
    }

    /**
     * Base64 encodes learning material contents for indexing
     * Large PDFs are split into multiple chunks to they can
     * be indexed without going over the size limit. Other non-text
     * or too large files are ignored.
     *
     * @param LearningMaterialDTO $dto
     * @return array
     */
    protected function extractLearningMaterialData(LearningMaterialDTO $dto): array
    {
        //skip files without useful text content
        if ($dto->mimetype && preg_match('/image|video|audio/', $dto->mimetype)) {
            return [];
        }
        $info = new SplFileInfo($dto->filename);
        $extension = $info->getExtension();
        if (in_array($extension, ['mp3', 'mov'])) {
            return [];
        }

        $data = $this->nonCachingIliosFileSystem->getFileContents($dto->relativePath);
        $encodedData = base64_encode($data);
        if (strlen($encodedData) < $this->uploadLimit) {
            return [
                $encodedData
            ];
        }
        if ($dto->mimetype === 'application/pdf') {
            try {
                $parts = $this->splitPDFIntoSmallParts($data);
                return array_map(function (string $string) {
                    return base64_encode($string);
                }, $parts);
            } catch (FpdiException $e) {
                $this->logger->error('Unable to split large PDF learning material into smaller parts.', [
                    'id' => $dto->id,
                    'filename' => $dto->filename,
                    'path' => $dto->relativePath,
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        }

        return [];
    }

    /**
     * Extracts each page from a PDF and attempts to create smaller PDFs with as many pages
     * as possible. This is better than returning all the pages individually because there
     * is significant overhead in each transaction with elasticsearch so we want to keep
     * the total PDF count down, but each one under the limit,.
     *
     * Thanks to https://gist.github.com/silasrm/3da655045b899a858eae4f4463755f5c for a
     * critical example.
     *
     * @param string $pdfContents
     * @return array
     * @throws FpdiException
     */
    protected function splitPDFIntoSmallParts(string $pdfContents): array
    {
        // Base64 Encoding makes files about 30% bigger so we need some padding when comparing
        $fileSizeLimit = $this->uploadLimit * .66;

        $i = 1;
        $pagesInCurrentPdf = 0;
        $PDFs = [];
        $sizeLimitReached = true;
        $newPdf = new Fpdi();
        $stream = StreamReader::createByString($pdfContents);
        $pageCount = $newPdf->setSourceFile($stream);
        // Keep adding new pages until we run out of space and then start over
        while ($i <= $pageCount) {
            if ($sizeLimitReached) {
                // start a new empty PDF
                $newPdf = new Fpdi();
                $newPdf->setSourceFile($stream);
                $sizeLimitReached = false;
            }
            //create a copy to see how big the output would be
            $testCopy = clone $newPdf;
            $this->addPageToPdf($testCopy, $i);
            $testOutput = $testCopy->Output('s');
            if (strlen($testOutput) < $fileSizeLimit) {
                //add the page and move on to the next one
                $this->addPageToPdf($newPdf, $i);
                $i++;
                $pagesInCurrentPdf++;
            } elseif ($pagesInCurrentPdf > 0) {
                //we've reached a point where adding another page is too much
                //instead we'll just output what we have and start again
                $output = $newPdf->Output('s');
                $PDFs[] = $output;
                $sizeLimitReached = true;
                $pagesInCurrentPdf = 0;
            } else {
                //this single page is too big so we have to skip it
                $i++;
            }
        }
        return $PDFs;
    }

    protected function addPageToPdf(Fpdi $pdf, int $pageNumber): void
    {
        $templateId = $pdf->importPage($pageNumber);
        $size = $pdf->getTemplateSize($templateId);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);
    }

    public static function getMapping(): array
    {
        return [
            'mappings' => [
                '_doc' => [
                    '_meta' => [
                        'version' => '1',
                    ],
                    'properties' => [
                        'learningMaterialId' => [
                            'type' => 'integer'
                        ],
                        'material' => [
                            'type' => 'object'
                        ],
                    ]
                ]
            ]
        ];
    }

    public static function getPipeline(): array
    {
        return [
            'id' => 'learning_materials',
            'body' => [
                'description' => 'Learning Material Data',
                'processors' => [
                    [
                        'attachment' => [
                            'field' => 'data',
                            'target_field' => 'material',
                        ]
                    ]
                ]
            ]
        ];
    }
}
