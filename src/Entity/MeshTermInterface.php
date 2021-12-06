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

    public function getMeshTermUid(): string;

    /**
     * @param string $lexicalTag
     */
    public function setLexicalTag($lexicalTag);

    public function getLexicalTag(): string;

    /**
     * @param bool $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred);

    public function isConceptPreferred(): bool;

    /**
     * @param bool $recordPreferred
     */
    public function setRecordPreferred($recordPreferred);

    public function isRecordPreferred(): bool;

    /**
     * @param bool $permuted
     */
    public function setPermuted($permuted);

    public function isPermuted(): bool;
}
