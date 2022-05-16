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
    public function setMeshTermUid(string $meshTermUid);
    public function getMeshTermUid(): string;

    public function setLexicalTag(?string $lexicalTag);
    public function getLexicalTag(): ?string;

    public function setConceptPreferred(?bool $conceptPreferred);
    public function isConceptPreferred(): ?bool;

    public function setRecordPreferred(?bool $recordPreferred);
    public function isRecordPreferred(): ?bool;

    public function setPermuted(?bool $permuted);
    public function isPermuted(): ?bool;
}
