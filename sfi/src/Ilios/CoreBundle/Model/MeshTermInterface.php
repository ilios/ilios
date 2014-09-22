<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface MeshTermInterface
 */
interface MeshTermInterface 
{
    public function setMeshTermUid($meshTermUid);

    public function getMeshTermUid();

    public function setName($name);

    public function getName();

    public function setLexicalTag($lexicalTag);

    public function getLexicalTag();

    public function setConceptPreferred($conceptPreferred);

    public function getConceptPreferred();

    public function setRecordPreferred($recordPreferred);

    public function getRecordPreferred();

    public function setPermuted($permuted);

    public function getPermuted();

    public function setPrint($print);

    public function getPrint();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();
}
