<?php

/**
 * Just a place to throw in Ilios-related default/fallback values.
 * Check the <code>config.php</code> file for (possible) overrides.
 */
interface Ilios2_Config_Defaults
{
    /**
     * Default number of days that an offering or an Independent Learning Session
     * should remain visually flagged on the calendar
     * after it (or its parent session) has been updated last.
     * @var int
     */
    const DEFAULT_VISUAL_ALERT_THRESHOLD_IN_DAYS = 3;
}
