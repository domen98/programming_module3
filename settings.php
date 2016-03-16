<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file adds the settings pages to the navigation menu
 *
 * @package   mod_prog
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/prog/adminlib.php');

$ADMIN->add('modsettings', new admin_category('modprogfolder', new lang_string('pluginname', 'mod_prog'), $module->is_enabled() === false));

$settings = new admin_settingpage($section, get_string('settings', 'mod_prog'), 'moodle/site:config', $module->is_enabled() === false);

if ($ADMIN->fulltree) {
    $menu = array();
    foreach (core_component::get_plugin_list('progfeedback') as $type => $notused) {
        $visible = !get_config('progfeedback_' . $type, 'disabled');
        if ($visible) {
            $menu['progfeedback_' . $type] = new lang_string('pluginname', 'progfeedback_' . $type);
        }
    }

    // The default here is feedback_comments (if it exists).
    $name = new lang_string('feedbackplugin', 'mod_prog');
    $description = new lang_string('feedbackpluginforgradebook', 'mod_prog');
    $settings->add(new admin_setting_configselect('prog/feedback_plugin_for_gradebook',
                                                  $name,
                                                  $description,
                                                  'progfeedback_comments',
                                                  $menu));

    $name = new lang_string('showrecentsubmissions', 'mod_prog');
    $description = new lang_string('configshowrecentsubmissions', 'mod_prog');
    $settings->add(new admin_setting_configcheckbox('prog/showrecentsubmissions',
                                                    $name,
                                                    $description,
                                                    0));

    $name = new lang_string('sendsubmissionreceipts', 'mod_prog');
    $description = new lang_string('sendsubmissionreceipts_help', 'mod_prog');
    $settings->add(new admin_setting_configcheckbox('prog/submissionreceipts',
                                                    $name,
                                                    $description,
                                                    1));

    $name = new lang_string('submissionstatement', 'mod_prog');
    $description = new lang_string('submissionstatement_help', 'mod_prog');
    $default = get_string('submissionstatementdefault', 'mod_prog');
    $settings->add(new admin_setting_configtextarea('prog/submissionstatement',
                                                    $name,
                                                    $description,
                                                    $default));

    $name = new lang_string('defaultsettings', 'mod_prog');
    $description = new lang_string('defaultsettings_help', 'mod_prog');
    $settings->add(new admin_setting_heading('defaultsettings', $name, $description));

    $name = new lang_string('alwaysshowdescription', 'mod_prog');
    $description = new lang_string('alwaysshowdescription_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/alwaysshowdescription',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('allowsubmissionsfromdate', 'mod_prog');
    $description = new lang_string('allowsubmissionsfromdate_help', 'mod_prog');
    $setting = new admin_setting_configduration('prog/allowsubmissionsfromdate',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('duedate', 'mod_prog');
    $description = new lang_string('duedate_help', 'mod_prog');
    $setting = new admin_setting_configduration('prog/duedate',
                                                    $name,
                                                    $description,
                                                    604800);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('cutoffdate', 'mod_prog');
    $description = new lang_string('cutoffdate_help', 'mod_prog');
    $setting = new admin_setting_configduration('prog/cutoffdate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('submissiondrafts', 'mod_prog');
    $description = new lang_string('submissiondrafts_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/submissiondrafts',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requiresubmissionstatement', 'mod_prog');
    $description = new lang_string('requiresubmissionstatement_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/requiresubmissionstatement',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(
        'none' => get_string('attemptreopenmethod_none', 'mod_prog'),
        'manual' => get_string('attemptreopenmethod_manual', 'mod_prog'),
        'untilpass' => get_string('attemptreopenmethod_untilpass', 'mod_prog')
    );
    $name = new lang_string('attemptreopenmethod', 'mod_prog');
    $description = new lang_string('attemptreopenmethod_help', 'mod_prog');
    $setting = new admin_setting_configselect('prog/attemptreopenmethod',
                                                    $name,
                                                    $description,
                                                    'none',
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(-1 => get_string('unlimitedattempts', 'mod_prog'));
    $options += array_combine(range(1, 30), range(1, 30));
    $name = new lang_string('maxattempts', 'mod_prog');
    $description = new lang_string('maxattempts_help', 'mod_prog');
    $setting = new admin_setting_configselect('prog/maxattempts',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmission', 'mod_prog');
    $description = new lang_string('teamsubmission_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/teamsubmission',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('preventsubmissionnotingroup', 'mod_prog');
    $description = new lang_string('preventsubmissionnotingroup_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/preventsubmissionnotingroup',
        $name,
        $description,
        0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requireallteammemberssubmit', 'mod_prog');
    $description = new lang_string('requireallteammemberssubmit_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/requireallteammemberssubmit',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmissiongroupingid', 'mod_prog');
    $description = new lang_string('teamsubmissiongroupingid_help', 'mod_prog');
    $setting = new admin_setting_configempty('prog/teamsubmissiongroupingid',
                                                    $name,
                                                    $description);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendnotifications', 'mod_prog');
    $description = new lang_string('sendnotifications_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/sendnotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendlatenotifications', 'mod_prog');
    $description = new lang_string('sendlatenotifications_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/sendlatenotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendstudentnotificationsdefault', 'mod_prog');
    $description = new lang_string('sendstudentnotificationsdefault_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/sendstudentnotifications',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('blindmarking', 'mod_prog');
    $description = new lang_string('blindmarking_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/blindmarking',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingworkflow', 'mod_prog');
    $description = new lang_string('markingworkflow_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/markingworkflow',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingallocation', 'mod_prog');
    $description = new lang_string('markingallocation_help', 'mod_prog');
    $setting = new admin_setting_configcheckbox('prog/markingallocation',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);
}

$ADMIN->add('modprogfolder', $settings);
// Tell core we already added the settings structure.
$settings = null;

$ADMIN->add('modprogfolder', new admin_category('progsubmissionplugins',
    new lang_string('submissionplugins', 'prog'), !$module->is_enabled()));
$ADMIN->add('progsubmissionplugins', new prog_admin_page_manage_prog_plugins('progsubmission'));
$ADMIN->add('modprogfolder', new admin_category('progfeedbackplugins',
    new lang_string('feedbackplugins', 'prog'), !$module->is_enabled()));
$ADMIN->add('progfeedbackplugins', new prog_admin_page_manage_prog_plugins('progfeedback'));

foreach (core_plugin_manager::instance()->get_plugins_of_type('progsubmission') as $plugin) {
    /** @var \mod_prog\plugininfo\progsubmission $plugin */
    $plugin->load_settings($ADMIN, 'progsubmissionplugins', $hassiteconfig);
}

foreach (core_plugin_manager::instance()->get_plugins_of_type('progfeedback') as $plugin) {
    /** @var \mod_prog\plugininfo\progfeedback $plugin */
    $plugin->load_settings($ADMIN, 'progfeedbackplugins', $hassiteconfig);
}
