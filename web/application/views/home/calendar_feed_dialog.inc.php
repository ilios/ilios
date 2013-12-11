<?php
/**
 * @file calendar_feed_dialog.inc.php
 *
 * Includes template.
 * Provides the markup for the calendar-feed API dialog in the calendar/dashboard page.
 */
?>
<div class="tabdialog" id="ical_feed_dialog">
    <div class="hd"><?php echo t('calendar.feed_title'); ?></div>
    <div class="bd">
        <p><?php echo t('calendar.feed_about'); ?></p>
        <p>
            <input style="font-size: smaller; width: 100%" id="apiurl" disabled/>
        </p>
        <p><?php echo t('calendar.feed_new_key'); ?></p>
    </div>
</div>
