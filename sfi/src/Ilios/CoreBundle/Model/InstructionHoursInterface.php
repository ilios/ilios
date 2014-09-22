<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface InstructionHoursInterface
 */
interface InstructionHoursInterface 
{
    public function getInstructionHoursId();

    public function setGenerationTimeStamp($generationTimeStamp);

    public function getGenerationTimeStamp();

    public function setHoursAccrued($hoursAccrued);

    public function getHoursAccrued();

    public function setModified($modified);

    public function getModified();

    public function setModificationTimeStamp($modificationTimeStamp);

    public function getModificationTimeStamp();

    public function setUserId($userId);

    public function getUserId();

    public function setSessionId($sessionId);

    public function getSessionId();
}
