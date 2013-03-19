<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-script that rigs the "discipline picker" dialog to the course management page.
 */
$parentModelGetterName = 'simpleCourseModelReturn';
$parentModelGetter = '
/*
 * dialog has an attribute set on it through its display handler which represents the
 * container number for which it is about to display.
 */
var ' . $parentModelGetterName . ' = function (dialog) {
    if (dialog.containerNumber != -1) {
        return ilios.cm.currentCourseModel.getSessionForContainer(dialog.containerNumber);
    }
    return ilios.cm.currentCourseModel;
}';

$localModelGetterInvocation = 'getDisciplines()';
$localModelSetterName = 'setDisciplines';

include getServerFilePath('views') . 'common/discipline_picker_include.php';
