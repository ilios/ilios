<?php
/**
 * @file report_details_view.inc.php
 *
 * Renders the markup for the report details view.
 *
 * Available template variables:
 *    $lang ... The language key.
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
                <span class="data-type"><?php echo $this->languagemap->t('general.phrases.report_name', $lang); ?></span>
                <span class="data" id="report-details-view-name"></span>
            </li>
            <li class="course-id">
                <span class="data-type"><?php echo $this->languagemap->t('general.phrases.academic_year', $lang); ?></span>
                <span class="data" id="report-details-view-academic-year"></span>
            </li>
            <li class="start-date">
                <span class="data-type"><?php echo $this->languagemap->t('general.phrases.start_date', $lang); ?></span>
                <span class="data" id="report-details-view-start-date"></span>
            </li>
            <li class="end-date">
                <span class="data-type"><?php echo $this->languagemap->t('general.phrases.end_date', $lang); ?></span>
                <span class="data" id="report-details-view-end-date"></span>
            </li>
        </ul>
    </div>
    <div style="display: none;" class="bd" id="report-details-view-content-wrapper">
        <div class="row">
            <div class="column label">
                <label for="report-details-description">
                    <?php echo $this->languagemap->t('general.terms.description', $lang); ?>
                </label>
            </div>
            <div class="column data" id="report-details-view-description"></div>
        </div>
        <div class="row">
            <div class="column label">
                <label for="report-details-program">
                    <?php echo $this->languagemap->t('general.terms.program', $lang); ?>
                </label>
            </div>
            <div class="column data" id="report-details-view-program"></div>
        </div>
        <div class="buttons bottom">
            <button disabled="disabled" class="medium radius button" id="report-details-view-edit-button">
                <?php echo $this->languagemap->t('general.terms.edit', $lang); ?>
            </button>
            <form id="report-details-view-export-form" action="<?php echo $controllerURL; ?>/export" method="GET" class="inline-form">
                <button class="medium radius button" id="report-details-view-export-button">
                    <?php echo $this->languagemap->t('general.terms.export', $lang); ?>
                </button>
                <input type="hidden" id="report-details-view-export-download-token" name="download_token" value="" />
                <input type="hidden" id="report-details-view-export-report-id" name="report_id" value="" />
            </form>
            <button disabled="disabled" class="medium radius button" id="report-details-view-delete-button">
                <?php echo $this->languagemap->t('general.phrases.delete', $lang); ?>
            </button>
        </div>
    </div>
</div>
