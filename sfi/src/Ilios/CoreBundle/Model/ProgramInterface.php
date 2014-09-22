<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface ProgramInterface
 */
interface ProgramInterface 
{
    public function getProgramId();

    public function setTitle($title);

    public function getTitle();

    public function setShortTitle($shortTitle);

    public function getShortTitle();

    public function setDuration($duration);

    public function getDuration();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setPublishedAsTbd($publishedAsTbd);

    public function getPublishedAsTbd();

    public function setOwningSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getOwningSchool();

    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null);

    public function getPublishEvent();
}
