<?php

declare(strict_types=1);

namespace App\Classes;

use App\Service\Config;
use Exception;
use OpenSearch\Client;
use OpenSearch\Exception\NotFoundHttpException;

class OpenSearchBase
{
    protected Client $client;
    protected bool $enabled = false;

    protected int|string|null $uploadLimit = null;

    public const int SIZE_LIMIT = 5000;
    protected const string SCROLL_TIME = '10s';
    protected string $languageAnalyzer = 'standard';

    /**
     * Search constructor.
     */
    public function __construct(
        Config $config,
        ?Client $client = null
    ) {
        if ($client) {
            $this->enabled = true;
            $this->client = $client;
        }
        $limit = $config->get('search_upload_limit');
        //10mb AWS hard limit on non-huge ES clusters and we need some overhead for control statements
        $this->uploadLimit = $limit ?? 9000000;

        $primaryLanguageOfInstruction =  $config->get('primaryLanguageOfInstruction');
        if ($primaryLanguageOfInstruction) {
            $this->languageAnalyzer = $primaryLanguageOfInstruction;
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    protected function doSearch(array $params): array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        return $this->client->search($params);
    }

    protected function doExplain(string $id, array $params): array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $params['id'] = $id;
        return $this->client->explain($params);
    }

    protected function doIndex(array $params): array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->index($params);
    }

    protected function doDelete(array $params): array
    {
        if (!$this->enabled) {
            return ['result' => 'deleted'];
        }
        try {
            return $this->client->delete($params);
        } catch (NotFoundHttpException) {
            return ['result' => 'deleted'];
        }
    }

    protected function doDeleteByQuery(array $params): array
    {
        if (!$this->enabled) {
            return ['failures' => []];
        }
        try {
            return $this->client->deleteByQuery($params);
        } catch (NotFoundHttpException) {
            return ['failures' => []];
        }
    }

    protected function doBulk(array $params): array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }

        $uri = "/_bulk";
        $body = '';
        foreach ($params['body'] ?? [] as $item) {
            $body .= json_encode($item, JSON_PRESERVE_ZERO_FRACTION + JSON_INVALID_UTF8_SUBSTITUTE) . "\n";
        }
        $compressedBody = gzencode($body);
        $rhett =  $this->client->request('POST', $uri, [
            'body' => $compressedBody,
            'options' => [
                'headers' => [
                    'Content-Encoding' => 'gzip',
                ],
            ],
        ]);

        //have to force the type, it comes back as iterable
        return (array) $rhett;
    }

    /**
     * The API for bulk indexing is a little bit weird and front data has to be inserted in
     * front of every item. This allows bulk indexing on many types at the same time, and
     * this convenience method takes care of that for us.
     */
    protected function doBulkIndex(string $index, array $items): bool
    {
        if (!$this->enabled || empty($items)) {
            return true;
        }

        $totalItems = count($items);
        $i = 0;
        $chunks = [];
        $chunk = [];
        $chunkSize = 0;
        // Keep adding items until we run out of space and then start over
        while ($i < $totalItems) {
            $item = $items[$i];
            $itemSize = strlen(json_encode($item, JSON_INVALID_UTF8_SUBSTITUTE));
            //upload limit multiplied for compression
            if (($chunkSize + $itemSize) < $this->uploadLimit) {
                //add the item and move on to the next one
                $chunk[] = $item;
                $i++;
                $chunkSize += $itemSize;
            } elseif ($chunk !== []) {
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
        //take care of the last iteration
        if (!empty($chunk)) {
            $chunks[] = $chunk;
        }

        $results = [
            'took' => 0,
            'errors' => false,
            'items' => [],
        ];

        foreach ($chunks as $chunk) {
            $body = [];
            foreach ($chunk as $item) {
                $body[] = ['index' => [
                    '_index' => $index,
                    '_id' => $item['id'],
                ]];
                $body[] = $item;
            }
            $rhett = $this->doBulk(['body' => $body]);
            $results['took'] += $rhett['took'];
            if ($rhett['errors']) {
                $results['errors'] = true;
            }
            $results['items'] = array_merge($results['items'], $rhett['items']);
        }

        if ($results['errors']) {
            $errors = array_map(function (array $item) {
                if (array_key_exists('error', $item['index'])) {
                    return $item['index']['error']['reason'];
                }

                return null;
            }, $results['items']);
            $clean = array_filter($errors);
            $str = join(';', array_unique($clean));
            $count = count($clean);
            throw new Exception("Failed to bulk index {$index} {$count} errors. Error text: {$str}");
        }

        return true;
    }

    protected function doScrollSearch(array $params): array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        $scroll = self::SCROLL_TIME;
        $params['scroll'] = $scroll;
        $response = $this->doSearch($params);
        $scrollId = $response['_scroll_id'];
        $hits = [];
        do {
            $hits = [...$hits, ...$response['hits']['hits']];
            $response = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => $scroll,
            ]);
            $scrollId = $response['_scroll_id'] ?? $scrollId;
        } while ($response['hits']['hits']);
        $this->client->clearScroll([
            'scroll_id' => $scrollId,
        ]);

        return $hits;
    }

    protected function doCount(array $params): array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }
        return $this->client->count($params);
    }
}
