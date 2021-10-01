<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

interface DataLoaderInterface
{
    /**
     * Get a single item from this loader
     * @return array
     */
    public function getOne();

    /**
     * Get all items from this loader
     * @return array
     */
    public function getAll();

    /**
     * Create a sample of this item
     * @return array
     */
    public function create();

    /**
     * Create an invalid sample of this item
     * @return array
     */
    public function createInvalid();

    /**
     * Create multiple samples of this item
     * @param int $count
     * @return array
     */
    public function createMany($count);

    /**
     * Create a JSON:API compatible version
     */
    public function createJsonApi(array $arr): object;

    /**
     * JSON:API bulk compatible data
     */
    public function createBulkJsonApi(array $arr): object;

    /**
     * Get the DTO for a data type
     */
    public function getDtoClass(): string;

    /**
     * Get all scalar fields for a data type
     */
    public function getScalarFields(): array;

    /**
     * Get all scalar fields for a data type
     */
    public function getIdField(): string;
}
