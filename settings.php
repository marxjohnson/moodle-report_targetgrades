<?php
/**
 * Defines the fields for the settings page
 *
 * @package report_targetgrades
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Taunton's College, Southampton, UK 2010
 */

$fields = array('',
    'shortname' => get_string('shortname'),
    'fullname' => get_string('fullname'),
    'idnumber' => get_string('idnumber'));

$roles = get_records('role');
foreach($roles as $key => $role) {
    $roles[$key] = $role->name;
}

$categories = get_records('course_categories');
foreach ($categories as $key => $category) {
    $categories[$key] = $category->name;
}

$infofields = get_records('user_info_field');
$userfields = array('');
foreach($infofields as $infofield) {
    $userfields[$infofield->shortname] = $infofield->name;
}

/**
 * Defines the number of characters to use for the pattern that matches courses
 * to a set of ALIS statistics
 */
$settings->add(new admin_setting_configtext('group_length', get_string('group_length', 'report_targetgrades'), '', '', PARAM_NUMBER));
$settings->settings->group_length->plugin='block/mtgdistribute';

/**
 * Defines the field to use for the pattern that matches courses against a set
 * of ALIS statistics
 */
$settings->add(new admin_setting_configselect('group_field', get_string('group_field', 'report_targetgrades'), get_string('group_fielddesc', 'report_targetgrades'), '', $fields));
$settings->settings->group_field->plugin='block/mtgdistribute';

/**
 * Defines the user field where the average GCSE scrore is stored
 */
$settings->add(new admin_setting_configselect('gcse_field', get_string('gcse_field', 'report_targetgrades'), get_string('gcse_fielddesc', 'report_targetgrades'), '', $userfields));
$settings->settings->gcse_field->plugin='block/mtgdistribute';

/**
 * Defines the roles to which target grades should be distributed
 */
$settings->add(new admin_setting_configmulticheckbox('roles', get_string('roles', 'report_targetgrades'), get_string('rolesdesc', 'report_targetgrades'), array('5'), $roles));
$settings->settings->roles->plugin='block/mtgdistribute';

/**
 * Defines the course categories that should be made available for distribution
 */
$settings->add(new admin_setting_configmulticheckbox('categories', get_string('categories', 'report_targetgrades'), get_string('categoriesdesc', 'report_targetgrades'), $categories, $categories));
$settings->settings->categories->plugin='block/mtgdistribute';

/**
 * Defines the field that should be matched to the regex to find which courses
 * should NOT be available for distribution
 */
$settings->add(new admin_setting_configselect('exclude_field', get_string('exclude_field', 'report_targetgrades'), '', '', $fields));
$settings->settings->exclude_field->plugin='block/mtgdistribute';

/**
 * Defines the regex that the above field should be matched against to find
 * courses which should NOT be available for distribution
 */
$settings->add(new admin_setting_configtext('exclude_regex', get_string('exclude_regex', 'report_targetgrades'), get_string('exclude_regexdesc', 'report_targetgrades'), ''));
$settings->settings->exclude_regex->plugin='block/mtgdistribute';

?>