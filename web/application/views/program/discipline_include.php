<?php
/**
 * Includes-script that rigs the "discipline picker" dialog to the program management page.
 */
$parentModelGetterName = 'getProgramYearModelAssociatedToDialog';
$parentModelGetter = '
/*
 * dialog has an attribute set on it through its display handler which represents the
 * container number for which it is about to display.
 */
var ' . $parentModelGetterName . ' = function (dialog) {
    var containerNumber = dialog.containerNumber;
    return ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
}';


$localModelGetterInvocation = 'getDisciplineArray()';
$localModelSetterName = 'setDisciplineArray';

include getServerFilePath('views') . 'common/discipline_picker_include.php';
