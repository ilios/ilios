<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Constant interface, defines all the user contexts for calendar events.
 * @package App\Classes
 */
interface CalendarEventUserContext
{
    public const string LEARNER = 'learner';

    public const string INSTRUCTOR = 'instructor';

    public const string COURSE_DIRECTOR = 'course director';

    public const string COURSE_ADMINISTRATOR = 'course administrator';

    public const string SESSION_ADMINISTRATOR = 'session administrator';
}
