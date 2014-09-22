<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface UserSyncExceptionInterface
 */
interface UserSyncExceptionInterface 
{
    public function getExceptionId();

    public function setProcessId($processId);

    public function getProcessId();

    public function setProcessName($processName);

    public function getProcessName();

    public function setExceptionCode($exceptionCode);

    public function getExceptionCode();

    public function setMismatchedPropertyName($mismatchedPropertyName);

    public function getMismatchedPropertyName();

    public function setMismatchedPropertyValue($mismatchedPropertyValue);

    public function getMismatchedPropertyValue();

    public function setUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getUser();
}
