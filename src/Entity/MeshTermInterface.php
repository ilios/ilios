<?php

namespace App\Entity;

use App\Traits\ConceptsEntityInterface;
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
    ConceptsEntityInterface
{

    /**
     * @param string $meshTermUid
     */
    public function setMeshTermUid($meshTermUid);

    /**
     * @return string
     */
    public function getMeshTermUid();

    /**
     * @param string $lexicalTag
     */
    public function setLexicalTag($lexicalTag);

    /**
     * @return string
     */
    public function getLexicalTag();

    /**
     * @param bool $conceptPreferred
     */
    public function setConceptPreferred($conceptPreferred);

    /**
     * @return bool
     */
    public function isConceptPreferred();

    /**
     * @param bool $recordPreferred
     */
    public function setRecordPreferred($recordPreferred);

    /**
     * @return bool
     */
    public function isRecordPreferred();

    /**
     * @param bool $permuted
     */
    public function setPermuted($permuted);

    /**
     * @return bool
     */
    public function isPermuted();
}
