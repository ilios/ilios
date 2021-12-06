<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ConceptsEntityInterface;
use App\Traits\CreatedAtEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshTermInterface
 */
interface MeshTermInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    ConceptsEntityInterface,
    CreatedAtEntityInterface
{

    /**
     * @param string $meshTermUid
     */
    public function setMeshTermUid($meshTermUid);

    /**
     * @return string
     */
    public function getMeshTermUid(): string;

    /**
     * @param string $lexicalTag
     */
    public function setLexicalTag($lexicalTag);

    /**
     * @return string
     */
    public function getLexicalTag(): string;

    /**
     * @param bool $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred);

    /**
     * @return bool
     */
    public function isConceptPreferred(): bool;

    /**
     * @param bool $recordPreferred
     */
    public function setRecordPreferred($recordPreferred);

    /**
     * @return bool
     */
    public function isRecordPreferred(): bool;

    /**
     * @param bool $permuted
     */
    public function setPermuted($permuted);

    /**
     * @return bool
     */
    public function isPermuted(): bool;
}
