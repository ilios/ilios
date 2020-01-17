<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Classes\IndexableCourse;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\UserDTO;
use Elasticsearch\Client;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Exception;
use Psr\Log\LoggerInterface;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\FpdiException;
use setasign\Fpdi\PdfParser\StreamReader;
use SplFileInfo;

class Index extends ElasticSearchBase
{
    /**
     * @var NonCachingIliosFileSystem
     */
    private $nonCachingIliosFileSystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var int */
    private $uploadLimit;

    public function __construct(
        NonCachingIliosFileSystem $nonCachingIliosFileSystem,
        Config $config,
        LoggerInterface $logger,
        Client $client = null
    ) {
        parent::__construct($client);
        $this->nonCachingIliosFileSystem = $nonCachingIliosFileSystem;
        $this->logger = $logger;
        $limit = $config->get('elasticsearch_upload_limit');
        //10mb AWS hard limit on non-huge ES clusters and we need some overhead for control statements
        $this->uploadLimit = $limit ?? 9000000;
    }

    /**
     * @param UserDTO[] $users
     * @return bool
     */
    public function indexUsers(array $users): bool
    {
        foreach ($users as $user) {
            if (!$user instanceof UserDTO) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$users must be an array of %s. %s found',
                        UserDTO::class,
                        get_class($user)
                    )
                );
            }
        }
        $input = array_map(function (UserDTO $user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'middleName' => $user->middleName,
                'displayName' => $user->displayName,
                'email' => $user->email,
                'campusId' => $user->campusId,
                'username' => $user->username,
                'enabled' => $user->enabled,
                'fullName' => $user->firstName . ' ' . $user->middleName . ' ' . $user->lastName,
                'fullNameLastFirst' => $user->lastName . ', ' . $user->firstName . ' ' . $user->middleName,
            ];
        }, $users);

        $result = $this->bulkIndex(Search::USER_INDEX, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $result = $this->delete([
            'index' => Search::USER_INDEX,
            'id' => $id,
        ]);

        return $result['result'] === 'deleted';
    }

    /**
     * @param IndexableCourse[] $courses
     * @return bool
     */
    public function indexCourses(array $courses): bool
    {
        foreach ($courses as $course) {
            if (!$course instanceof IndexableCourse) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$courses must be an array of %s. %s found',
                        IndexableCourse::class,
                        get_class($course)
                    )
                );
            }
        }

        $input = array_reduce($courses, function (array $carry, IndexableCourse $item) {
            $sessions = $item->createIndexObjects();
            $sessionsWithMaterials = $this->attachLearningMaterialsToSession($sessions);
            return array_merge($carry, $sessionsWithMaterials);
        }, []);

        $result = $this->bulkIndex(Search::CURRICULUM_INDEX, $input);

        if ($result['errors']) {
            $errors = array_map(function (array $item) {
                if (array_key_exists('error', $item['index'])) {
                    return $item['index']['error']['reason'];
                }
            }, $result['items']);
            $clean = array_filter($errors);
            $str = join(';', array_unique($clean));
            $count = count($clean);
            throw new Exception("Failed to index all courses ${count} errors. Error text: ${str}");
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteCourse(int $id): bool
    {
        $result = $this->deleteByQuery([
            'index' => Search::CURRICULUM_INDEX,
            'body' => [
                'query' => [
                    'term' => ['courseId' => $id]
                ]
            ]
        ]);

        return !count($result['failures']);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteSession(int $id): bool
    {
        $result = $this->delete([
            'index' => Search::CURRICULUM_INDEX,
            'id' => ElasticSearchBase::SESSION_ID_PREFIX . $id
        ]);

        return $result['result'] === 'deleted';
    }

    /**
     * @param Descriptor[] $descriptors
     * @return bool
     */
    public function indexMeshDescriptors(array $descriptors): bool
    {
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof Descriptor) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '$descriptors must be an array of %s. %s found',
                        Descriptor::class,
                        get_class($descriptor)
                    )
                );
            }
        }

        $input = array_map(function (Descriptor $descriptor) {
            $conceptMap = array_reduce($descriptor->getConcepts(), function (array $carry, Concept $concept) {
                $carry['conceptNames'][] = $concept->getName();
                $carry['scopeNotes'][] = $concept->getScopeNote();
                $carry['casn1Names'][] = $concept->getCasn1Name();
                foreach ($concept->getTerms() as $term) {
                    $carry['termNames'][] = $term->getName();
                }

                return $carry;
            }, [
                'conceptNames' => [],
                'termNames' => [],
                'scopeNotes' => [],
                'casn1Names' => [],
            ]);

            return [
                'id' => $descriptor->getUi(),
                'name' => $descriptor->getName(),
                'annotation' => $descriptor->getAnnotation(),
                'previousIndexing' => join(' ', $descriptor->getPreviousIndexing()),
                'terms' => join(' ', $conceptMap['termNames']),
                'concepts' => join(' ', $conceptMap['conceptNames']),
                'scopeNotes' => join(' ', $conceptMap['scopeNotes']),
                'casn1Names' => join(' ', $conceptMap['casn1Names']),
            ];
        }, $descriptors);

        $result = $this->bulkIndex(Search::MESH_INDEX, $input);
        return !$result['errors'];
    }

    /**
     * @param LearningMaterialDTO[] $materials
     * @return bool
     */
    public function indexLearningMaterials(array $materials): bool
    {
        foreach ($materials as $material) {
            if (!$material instanceof LearningMaterialDTO) {
                throw new \InvalidArgumentException(
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
                'index' => self::LEARNING_MATERIAL_INDEX,
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
    public function deleteLearningMaterial(int $id): bool
    {
        $result = $this->deleteByQuery([
            'index' => Search::LEARNING_MATERIAL_INDEX,
            'body' => [
                'query' => [
                    'term' => ['learningMaterialId' => $id]
                ]
            ]
        ]);

        return !count($result['failures']);
    }

    protected function index(array $params): array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->index($params);
    }

    protected function delete(array $params): array
    {
        if (!$this->enabled) {
            return ['result' => 'deleted'];
        }
        return $this->client->delete($params);
    }

    protected function deleteByQuery(array $params): array
    {
        if (!$this->enabled) {
            return ['failures' => []];
        }
        return $this->client->deleteByQuery($params);
    }

    protected function bulk(array $params): array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->bulk($params);
    }

    /**
     * The API for bulk indexing is a little bit weird and front data has to be inserted in
     * front of every item. This allows bulk indexing on many types at the same time, and
     * this convenience method takes care of that for us.
     * @param $index
     * @param array $items
     * @return array
     */
    protected function bulkIndex(string $index, array $items): array
    {
        if (!$this->enabled || empty($items)) {
            return ['errors' => false];
        }

        $totalItems = count($items);
        $i = 0;
        $chunks = [];
        $chunk = [];
        $chunkSize = 0;
        // Keep adding items until we run out of space and then start over
        while ($i < $totalItems) {
            $item = $items[$i];
            $itemSize = strlen(json_encode($item));
            if (($chunkSize + $itemSize) < $this->uploadLimit) {
                //add the item and move on to the next one
                $chunk[] = $item;
                $i++;
                $chunkSize += $itemSize;
            } else {
                if (count($chunk)) {
                    //we've reached a point where adding another item is too much
                    //instead we'll just save what we have and start again
                    $chunks[] = $chunk;
                    $chunk = [];
                    $chunkSize = 0;
                } else {
                    //this single item is too big so we have to skip it
                    throw new Exception(
                        sprintf(
                            'Unable to index %s ID #%s as it is larger than the %s byte upload limit',
                            $index,
                            $item['id'],
                            $this->uploadLimit
                        )
                    );
                }
            }
        }
        //take care of the last iteration
        if (!empty($chunk)) {
            $chunks[] = $chunk;
        }

        $results = [
            'took' => 0,
            'errors' => false,
            'items' => []
        ];

        foreach ($chunks as $chunk) {
            $body = [];
            foreach ($chunk as $item) {
                $body[] = ['index' => [
                    '_index' => $index,
                    '_type' => '_doc',
                    '_id' => $item['id']
                ]];
                $body[] = $item;
            }
            $rhett = $this->bulk(['body' => $body]);
            $results['took'] += $rhett['took'];
            if ($rhett['errors']) {
                $results['errors'] = true;
            }
            $results['items'] = array_merge($results['items'], $rhett['items']);
        }

        return $results;
    }

    protected function attachLearningMaterialsToSession(array $sessions): array
    {
        $courseIds = array_column($sessions, 'courseFileLearningMaterialIds');
        $sessionIds = array_column($sessions, 'sessionFileLearningMaterialIds');
        $learningMaterialIds = array_values(array_unique(array_merge([], ...$courseIds, ...$sessionIds)));
        $materialsById = [];
        if (!empty($learningMaterialIds)) {
            $params = [
                'type' => '_doc',
                'index' => self::LEARNING_MATERIAL_INDEX,
                'body' => [
                    'query' => [
                        'terms' => [
                            'learningMaterialId' => $learningMaterialIds
                        ]
                    ],
                    "_source" => [
                        'learningMaterialId',
                        'material.content',
                    ]
                ]
            ];
            $results = $this->client->search($params);

            $materialsById = array_reduce($results['hits']['hits'], function (array $carry, array $hit) {
                $result = $hit['_source'];
                $id = $result['learningMaterialId'];

                if (array_key_exists('material', $result)) {
                    $carry[$id][] = $result['material']['content'];
                }

                return $carry;
            }, []);
        }

        return array_map(function (array $session) use ($materialsById) {
            foreach ($session['sessionFileLearningMaterialIds'] as $id) {
                if (array_key_exists($id, $materialsById)) {
                    foreach ($materialsById[$id] as $value) {
                        $session['sessionLearningMaterialAttachments'][] = $value;
                    }
                }
            }
            unset($session['sessionFileLearningMaterialIds']);
            foreach ($session['courseFileLearningMaterialIds'] as $id) {
                if (array_key_exists($id, $materialsById)) {
                    foreach ($materialsById[$id] as $value) {
                        $session['courseLearningMaterialAttachments'][] = $value;
                    }
                }
            }
            unset($session['courseFileLearningMaterialIds']);

            return $session;
        }, $sessions);
    }

    /**
     * Indexes have been renamed, this cleans up after ourselves
     */
    protected function cleanupOldIndexes()
    {
        $indexes = [
            'ilios-public-curriculum',
            'ilios-public-mesh',
            'ilios-private-users',
        ];
        foreach ($indexes as $index) {
            if ($this->client->indices()->exists(['index' => $index])) {
                $this->client->indices()->delete(['index' => $index]);
            }
        }
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
                $newPdf = new FPDI();
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
            } else {
                if ($pagesInCurrentPdf > 0) {
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
        }
        return $PDFs;
    }

    protected function addPageToPdf(FPDI $pdf, int $pageNumber): void
    {
        $templateId = $pdf->importPage($pageNumber);
        $size = $pdf->getTemplateSize($templateId);
        $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
        $pdf->useTemplate($templateId);
    }
}
