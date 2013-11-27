<?php
/**
 * @file report_details_view.inc.php
 *
 * Renders the markup for the report details view.
 *
 * Available template variables:
 *
 *    $controllerURL ... The url to "curriculum inventory manager" controller.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/js/ilios.cim.js
 */
?>
<div class="entity_container level-1" id="report-details-view-container" style="display:none">
    <div class="hd clearfix">
        <div class="toggle">
            <a id="report-details-view-toggle" href="#">
                <i aria-hidden="true" class="icon-plus"> </i>
            </a>
        </div>
        <ul>
            <li class="title">
                <span class="data-type"><?php echo t('general.phrases.report_name'); ?></span>
                <span class="data" id="report-details-view-name"></span>
            </li>
            <li class="course-id">
                <span class="data-type"><?php echo t('general.phrases.academic_year'); ?></span>
                <span class="data" id="report-details-view-academic-year"></span>
            </li>
            <li class="start-date">
                <span class="data-type"><?php echo t('general.phrases.start_date'); ?></span>
                <span class="data" id="report-details-view-start-date"></span>
            </li>
            <li class="end-date">
                <span class="data-type"><?php echo t('general.phrases.end_date'); ?></span>
                <span class="data" id="report-details-view-end-date"></span>
            </li>
            <li class="publish-status">
                <span class="data-type"><?php echo t('general.terms.status'); ?></span>
                <span class="data">
                    <span class="status" id="report-details-status"></span>
                </span>
            </li>
        </ul>
    </div>
    <div style="display: none;" class="bd" id="report-details-view-content-wrapper">
        <div class="row">
            <div class="column label">
                <label for="report-details-description">
                    <?php echo t('general.terms.description'); ?>
                </label>
            </div>
            <div class="column data" id="report-details-view-description"></div>
        </div>
        <div class="row">
            <div class="column label">
                <label for="report-details-program">
                    <?php echo t('general.terms.program'); ?>
                </label>
            </div>
            <div class="column data" id="report-details-view-program"></div>
        </div>
        <div class="buttons bottom">
            <button disabled="disabled" class="medium radius button hidden" id="report-details-view-edit-button">
                <?php echo t('general.terms.edit'); ?>
            </button>
            <form id="report-details-view-export-form" action="<?php echo $controllerURL; ?>/export" method="GET"
                  class="inline-form hidden">
                <button disabled="disabled" class="medium radius button" id="report-details-view-export-button">
                    <?php echo t('general.terms.export'); ?>
                </button>
                <input type="hidden" id="report-details-view-export-download-token" name="download_token" value="" />
                <input type="hidden" id="report-details-view-export-report-id" name="report_id" value="" />
            </form>
            <button disabled="disabled" class="medium radius button hidden" id="report-details-view-finalize-button">
                <?php echo t('general.terms.finalize'); ?>
            </button>
            <button disabled="disabled" class="medium radius button hidden" id="report-details-view-delete-button">
                <?php echo t('general.phrases.delete'); ?>
            </button>
            <form id="report-details-view-download-form" action="<?php echo $controllerURL; ?>/download" method="GET"
                  class="inline-form hidden">
                <button disabled="disabled" class="medium radius button" id="report-details-view-download-button">
                    <?php echo t('general.terms.download'); ?>
                </button>
                <input type="hidden" id="report-details-view-download-download-token" name="download_token" value="" />
                <input type="hidden" id="report-details-view-download-report-id" name="report_id" value="" />
            </form>
        </div>
    </div>
</div>
