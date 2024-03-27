<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Constant interface, defines all the user contexts for calendar events.
 * @package App\Classes
 */
interface CalendarEventUserContext
{
    public const LEARNER = 'learner';

    public const INSTRUCTOR = 'instructor';

    public const COURSE_DIRECTOR = 'course director';

    public const COURSE_ADMINISTRATOR = 'course administrator';

    public const SESSION_ADMINISTRATOR = 'session administrator';
}
