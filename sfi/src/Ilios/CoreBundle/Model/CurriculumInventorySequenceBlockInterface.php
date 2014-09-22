<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface CurriculumInventorySequenceBlockInterface
 */
interface CurriculumInventorySequenceBlockInterface 
{
    public function getSequenceBlockId();

    public function setRequired($required);

    public function getRequired();

    public function setChildSequenceOrder($childSequenceOrder);

    public function getChildSequenceOrder();

    public function setOrderInSequence($orderInSequence);

    public function getOrderInSequence();

    public function setMinimum($minimum);

    public function getMinimum();

    public function setMaximum($maximum);

    public function getMaximum();

    public function setTrack($track);

    public function getTrack();

    public function setDescription($description);

    public function getDescription();

    public function setTitle($title);

    public function getTitle();

    public function setStartDate($startDate);

    public function getStartDate();

    public function setEndDate($endDate);

    public function getEndDate();

    public function setDuration($duration);

    public function getDuration();

    public function setAcademicLevel(\Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel $academicLevel = null);

    public function getAcademicLevel();

    public function setCourse(\Ilios\CoreBundle\Model\Course $course = null);

    public function getCourse();

    public function setParentSequenceBlock(;

    public function getParentSequenceBlock();

    public function setReport(\Ilios\CoreBundle\Model\CurriculumInventoryReport $report = null);

    public function getReport();
}

