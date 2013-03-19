<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Prints out the markup for the admin navigation.
 *
 * @todo replace direct calls to i18nVendor model with calls to helper function. see ticket #2567
 */
$tabs = array(
    array(
        'label' => $this->languagemap->getI18NString('general.terms.home', $lang),
        'target' => 'dashboard_controller',
        'id' => 't-home'
    ),
    array(
        'label' => $this->languagemap->getI18NString('general.terms.programs', $lang),
        'target' => 'program_management',
        'id' => 't-program'
    ),
    array(
        'label' => $this->languagemap->getI18NString('general.terms.instructors', $lang),
        'target' => 'instructor_group_management',
        'id' => 't-instructor'
    ),
    array(
        'label' => $this->languagemap->getI18NString('general.phrases.learner_groups', $lang),
        'target' => 'group_management',
        'id' => 't-learner'
    ),
    array(
        'label' => $this->languagemap->getI18NString('general.phrases.courses_and_sessions', $lang),
        'target' => 'course_management',
        'id' => 't-course'
    )
);
?>
<nav id="topnav">
    <ul class="tabs clearfix">
<?php
foreach ($tabs as $tabItem) :
?>
        <li id="<?php echo $tabItem['id'];?>">
            <a href="<?php echo site_url() . '/' . $tabItem['target']; ?>"><?php echo $tabItem['label']; ?> </a>
        </li>
<?php
endforeach;
?>
    </ul>
</nav> <!-- end #topnav -->
