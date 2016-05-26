<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionManagerInterface;

class CourseRollover {
    /**
     * @var CourseManagerInterface
     */
    protected $courseManager;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var OfferingManagerInterface
     */
    protected $offeringManager;

    /**
     * @param CourseManagerInterface $courseManager
     * @param SessionManagerInterface $sessionManager
     * @param OfferingManagerInterface $offeringManager
     */
    public function __construct(
        CourseManagerInterface $courseManager,
        SessionManagerInterface $sessionManager,
        OfferingManagerInterface $offeringManager
    ) {
        $this->courseManager = $courseManager;
        $this->sessionManager = $sessionManager;
        $this->offeringManager = $offeringManager;
    }

    public function foo () {
        echo "foo bar";
    }
}