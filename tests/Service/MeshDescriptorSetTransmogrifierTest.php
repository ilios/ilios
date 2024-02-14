<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\MeshDescriptorSetTransmogrifier;
use App\Tests\TestCase;
use DateTime;
use Ilios\MeSH\Model\AllowableQualifier;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Model\Reference;
use Ilios\MeSH\Model\Term;

/**
 * Class MeshDescriptorSetTransmogrifierTest
 * @covers \App\Service\MeshDescriptorSetTransmogrifier
 */
class MeshDescriptorSetTransmogrifierTest extends TestCase
{
    protected MeshDescriptorSetTransmogrifier $transmogrifier;

    public function setUp(): void
    {
        parent::setUp();
        $this->transmogrifier = new MeshDescriptorSetTransmogrifier();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->transmogrifier);
    }

    public function testTransmogrify(): void
    {
        $descriptor1 = new Descriptor();
        $descriptor1->setUi('D01');
        $descriptor2 = new Descriptor();
        $descriptor2->setUi('D02');
        $descriptor3 = new Descriptor();
        $descriptor3->setUi('D03');

        $existingDescriptorIds = [ 'D03' ];

        $descriptors = new DescriptorSet();
        $descriptors->addDescriptor($descriptor1);
        $descriptors->addDescriptor($descriptor2);
        $descriptors->addDescriptor($descriptor3);

        $ref1 = new Reference();
        $ref1->setUi('Q01');
        $ref2 = new Reference();
        $ref2->setUi('Q02');
        $ref3 = new Reference();
        $ref3->setUi('Q03');
        $duplicateRef1 = new Reference();
        $duplicateRef1->setUi($ref1->getUi());
        $qualifier1 = new AllowableQualifier();
        $qualifier1->setQualifierReference($ref1);
        $qualifier2 = new AllowableQualifier();
        $qualifier2->setQualifierReference($ref2);
        $qualifier3 = new AllowableQualifier();
        $qualifier3->setQualifierReference($ref3);
        $duplicateQualifier = new AllowableQualifier();
        $duplicateQualifier->setQualifierReference($duplicateRef1);
        $descriptor1->addAllowableQualifier($qualifier1);
        $descriptor1->addAllowableQualifier($qualifier2);
        $descriptor2->addAllowableQualifier($qualifier3);
        $descriptor2->addAllowableQualifier($duplicateQualifier);

        $concept1 = new Concept();
        $concept1->setUi('M01');
        $concept2 = new Concept();
        $concept2->setUi('M02');
        $concept3 = new Concept();
        $concept3->setUi('M03');
        $descriptor1->addConcept($concept1);
        $descriptor2->addConcept($concept2);
        $descriptor2->addConcept($concept3);

        $term1 = new Term();
        $term1->setUi('T01');
        $term1->setName('Term 1');
        $term1->setLexicalTag('ABB');
        $term1->setConceptPreferred(true);
        $term1->setRecordPreferred(false);
        $term1->setPermuted(false);

        $term2 = new Term();
        $term2->setUi('T02');
        $term2->setName('Term 2');
        $term2->setLexicalTag('ABX');
        $term2->setConceptPreferred(false);
        $term2->setRecordPreferred(true);
        $term2->setPermuted(true);

        $term3 = new Term();
        $term3->setUi('T03');
        $term3->setName('Term 3');
        $term3->setLexicalTag('ACR');
        $term3->setConceptPreferred(false);
        $term3->setRecordPreferred(true);
        $term3->setPermuted(true);

        $term4 = new Term();
        $term4->setUi('T04');
        $term4->setName('Term 4');
        $term4->setLexicalTag('NAM');
        $term4->setConceptPreferred(false);
        $term4->setRecordPreferred(true);
        $term4->setPermuted(true);

        $concept1->addTerm($term1);
        $concept2->addTerm($term2);
        $concept3->addTerm($term3);
        $concept3->addTerm($term4);

        $descriptor1->addPreviousIndexing('Foo');
        $descriptor1->addPreviousIndexing('Bar');
        $descriptor3->addPreviousIndexing('Baz');

        $descriptor1->addTreeNumber('1.1');
        $descriptor1->addTreeNumber('1.2');
        $descriptor3->addTreeNumber('3.1');

        $data = $this->transmogrifier->transmogrify($descriptors, $existingDescriptorIds);

        $this->assertEquals(2, count($data['insert']['mesh_descriptor']));
        $this->assertEquals($data['insert']['mesh_descriptor']['D01'], $descriptor1);
        $this->assertEquals($data['insert']['mesh_descriptor']['D02'], $descriptor2);

        $this->assertEquals(1, count($data['update']['mesh_descriptor']));
        $this->assertEquals($data['update']['mesh_descriptor']['D03'], $descriptor3);

        $this->assertEquals(4, count($data['insert']['mesh_descriptor_x_qualifier']));
        $this->assertEquals(['D01', 'Q01'], $data['insert']['mesh_descriptor_x_qualifier'][0]);
        $this->assertEquals(['D01', 'Q02'], $data['insert']['mesh_descriptor_x_qualifier'][1]);
        $this->assertEquals(['D02', 'Q03'], $data['insert']['mesh_descriptor_x_qualifier'][2]);
        $this->assertEquals(['D02', 'Q01'], $data['insert']['mesh_descriptor_x_qualifier'][3]);

        $this->assertEquals(3, count($data['insert']['mesh_qualifier']));
        $this->assertEquals($duplicateQualifier, $data['insert']['mesh_qualifier']['Q01']);
        $this->assertEquals($qualifier2, $data['insert']['mesh_qualifier']['Q02']);
        $this->assertEquals($qualifier3, $data['insert']['mesh_qualifier']['Q03']);

        $this->assertEquals(3, count($data['insert']['mesh_concept']));
        $this->assertEquals($concept1, $data['insert']['mesh_concept']['M01']);
        $this->assertEquals($concept2, $data['insert']['mesh_concept']['M02']);
        $this->assertEquals($concept3, $data['insert']['mesh_concept']['M03']);

        $this->assertEquals(3, count($data['insert']['mesh_descriptor_x_concept']));
        $this->assertEquals(['D01', 'M01'], $data['insert']['mesh_descriptor_x_concept'][0]);
        $this->assertEquals(['D02', 'M02'], $data['insert']['mesh_descriptor_x_concept'][1]);
        $this->assertEquals(['D02', 'M03'], $data['insert']['mesh_descriptor_x_concept'][2]);

        $this->assertEquals(4, count($data['insert']['mesh_term']));
        $this->assertEquals($term1, $data['insert']['mesh_term']['f2f897155cc34c82854374c95aa7265c']);
        $this->assertEquals($term2, $data['insert']['mesh_term']['5edc2ddc166bdb35f40a5d5588bec92b']);
        $this->assertEquals($term3, $data['insert']['mesh_term']['02184148ab7c834193206aef1689eea7']);
        $this->assertEquals($term4, $data['insert']['mesh_term']['ea9ae8ecee1f8a46784000abdcd0228a']);

        $this->assertEquals(4, count($data['insert']['mesh_concept_x_term']));
        $this->assertEquals(['M01', 'f2f897155cc34c82854374c95aa7265c'], $data['insert']['mesh_concept_x_term'][0]);
        $this->assertEquals(['M02', '5edc2ddc166bdb35f40a5d5588bec92b'], $data['insert']['mesh_concept_x_term'][1]);
        $this->assertEquals(['M03', '02184148ab7c834193206aef1689eea7'], $data['insert']['mesh_concept_x_term'][2]);
        $this->assertEquals(['M03', 'ea9ae8ecee1f8a46784000abdcd0228a'], $data['insert']['mesh_concept_x_term'][3]);

        $this->assertEquals(2, count($data['insert']['mesh_previous_indexing']));
        $this->assertEquals('Bar', $data['insert']['mesh_previous_indexing']['D01']);
        $this->assertEquals('Baz', $data['insert']['mesh_previous_indexing']['D03']);

        $this->assertEquals(2, count($data['insert']['mesh_tree']));
        $this->assertEquals(['1.1', '1.2'], $data['insert']['mesh_tree']['D01']);
        $this->assertEquals(['3.1'], $data['insert']['mesh_tree']['D03']);
    }

    public function testHashTerm(): void
    {
        $term1 = new Term();
        // relevant props
        $term1->setUi('T00001');
        $term1->setName('Term 1');
        $term1->setLexicalTag('ABCD');
        $term1->setConceptPreferred(true);
        $term1->setRecordPreferred(true);
        $term1->setPermuted(false);
        // non-relevant props
        $term1->setNote('Lorem ipsum');
        $term1->setAbbreviation('T1');
        $term1->setEntryVersion('1.0');
        $term1->setDateCreated(new DateTime());
        $term1->setSortVersion('1.0');
        $term1->addThesaurusId('124');

        $md5 = 'af9d0d3c0ee7ab0a9f008efab0741550';
        $this->assertEquals($md5, $this->transmogrifier->hashTerm($term1), 'Term gets hashed correctly.');
        $term1->setNote('Yabba Dabba Doo');
        $this->assertEquals(
            $md5,
            $this->transmogrifier->hashTerm($term1),
            'Modifying irrelevant property does not change generated hash'
        );

        $term1->setAbbreviation('T2');
        $this->assertEquals(
            $md5,
            $this->transmogrifier->hashTerm($term1),
            'Modifying irrelevant property does not change generated hash'
        );
        $term1->setEntryVersion('1.30');
        $this->assertEquals(
            $md5,
            $this->transmogrifier->hashTerm($term1),
            'Modifying irrelevant property does not change generated hash'
        );

        $term1->setDateCreated(new DateTime('2 weeks ago'));
        $this->assertEquals(
            $md5,
            $this->transmogrifier->hashTerm($term1),
            'Modifying irrelevant property does not change generated hash'
        );

        $term1->setSortVersion('11.0');
        $this->assertEquals(
            $md5,
            $this->transmogrifier->hashTerm($term1),
            'Modifying irrelevant property does not change generated hash'
        );

        $term1->addThesaurusId('555555');
        $this->assertEquals(
            $md5,
            $this->transmogrifier->hashTerm($term1),
            'Modifying irrelevant property does not change generated hash'
        );

        $term1->setUi('T00002');
        $this->assertEquals(
            '5e4e698a1dce61ebc328d60a96850d75',
            $this->transmogrifier->hashTerm($term1),
            'Term gets hashed correctly.'
        );

        $term1->setName('Term 2');
        $this->assertEquals(
            'f96bba1470504d36be64ddf34cefc67a',
            $this->transmogrifier->hashTerm($term1),
            'Term gets hashed correctly.'
        );

        $term1->setLexicalTag('ZZZZZ');
        $this->assertEquals(
            '7554be076dfd222ed173113340d21260',
            $this->transmogrifier->hashTerm($term1),
            'Term gets hashed correctly.'
        );

        $term1->setConceptPreferred(false);
        $this->assertEquals(
            '4d8241b51a3072256bc7f0d76945a122',
            $this->transmogrifier->hashTerm($term1),
            'Term gets hashed correctly.'
        );

        $term1->setRecordPreferred(false);
        $this->assertEquals(
            '2bc604689b540b3629b3a0d08f9f0c87',
            $this->transmogrifier->hashTerm($term1),
            'Term gets hashed correctly.'
        );

        $term1->setPermuted(true);
        $this->assertEquals(
            '933bbf10b83a2b3b9231409695928906',
            $this->transmogrifier->hashTerm($term1),
            'Term gets hashed correctly.'
        );
    }
}
