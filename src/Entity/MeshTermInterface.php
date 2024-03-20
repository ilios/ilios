<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ConceptsEntityInterface;
use App\Traits\CreatedAtEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\IdentifiableEntityInterface;

interface MeshTermInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    ConceptsEntityInterface,
    CreatedAtEntityInterface
{
    public function setMeshTermUid(string $meshTermUid): void;
    public function getMeshTermUid(): string;

    public function setLexicalTag(?string $lexicalTag): void;
    public function getLexicalTag(): ?string;

    public function setConceptPreferred(?bool $conceptPreferred): void;
    public function isConceptPreferred(): ?bool;

    public function setRecordPreferred(?bool $recordPreferred): void;
    public function isRecordPreferred(): ?bool;

    public function setPermuted(?bool $permuted): void;
    public function isPermuted(): ?bool;
}
