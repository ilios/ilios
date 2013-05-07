<div class="entity_container level-1" id="report-details-view-container" style="display:none">
    <div class="hd clearfix">
        <div class="toggle">
            <a id="report-details-view-toggle" href="#">
                <i aria-hidden="true" class="icon-plus"> </i>
            </a>
        </div>
        <ul>
            <li class="title">
                <span class="data-type">Report Name</span>
                <span class="data" id="report-details-view-name"></span>
            </li>
            <li class="course-id">
                <span class="data-type">Academic Year</span>
                <span class="data" id="report-details-view-academic-year"></span>
            </li>
            <li class="start-date">
                <span class="data-type">Start Date</span>
                <span class="data" id="report-details-view-start-date"></span>
            </li>
            <li class="end-date">
                <span class="data-type">End Date</span>
                <span class="data" id="report-details-view-end-date"></span>
            </li>
        </ul>
    </div>
    <div style="display: none;" class="bd" id="report-details-view-content-wrapper">
        <div class="row">
            <div class="column label">
                <label for="report-details-description">Description</label>
            </div>
            <div class="column data" id="report-details-view-description"></div>
        </div>
        <div class="row">
            <div class="column label">
                <label for="report-details-program">Program</label>
            </div>
            <div class="column data" id="report-details-view-program"></div>
        </div>
        <div class="buttons bottom">
            <button disabled="disabled" class="medium radius button" id="report-details-view-export-button">Export</button>
            <button disabled="disabled" class="medium radius button" id="report-details-view-edit-button">Edit Report</button>
        </div>
    </div>
</div>
