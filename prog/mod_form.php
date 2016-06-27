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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   mod_prog
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/prog/locallib.php');

/**
 * Assignment settings form.
 *
 * @package   mod_prog
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_prog_mod_form extends moodleform_mod {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $CFG, $COURSE, $DB, $PAGE;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('assignmentname', 'prog'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements(get_string('description', 'prog'));

        $mform->addElement('filemanager', 'introattachments',
                            get_string('introattachments', 'prog'),
                            null, array('subdirs' => 0, 'maxbytes' => $COURSE->maxbytes) );
        $mform->addHelpButton('introattachments', 'introattachments', 'prog');

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('prog', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }
        $assignment = new prog($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $assignment->set_course($course);
        }

        $config = get_config('prog');

        $mform->addElement('header', 'availability', get_string('availability', 'prog'));
        $mform->setExpanded('availability', true);

        $name = get_string('allowsubmissionsfromdate', 'prog');
        $options = array('optional'=>true);
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', $name, $options);
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'prog');

        $name = get_string('duedate', 'prog');
        $mform->addElement('date_time_selector', 'duedate', $name, array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'prog');

        $name = get_string('cutoffdate', 'prog');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, array('optional'=>true));
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'prog');

        $name = get_string('alwaysshowdescription', 'prog');
        $mform->addElement('checkbox', 'alwaysshowdescription', $name);
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'prog');
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]', 'notchecked');

        $assignment->add_all_plugin_settings($mform);

        $mform->addElement('header', 'submissionsettings', get_string('submissionsettings', 'prog'));

        $name = get_string('submissiondrafts', 'prog');
        $mform->addElement('selectyesno', 'submissiondrafts', $name);
        $mform->addHelpButton('submissiondrafts', 'submissiondrafts', 'prog');

        $name = get_string('requiresubmissionstatement', 'prog');
        $mform->addElement('selectyesno', 'requiresubmissionstatement', $name);
        $mform->addHelpButton('requiresubmissionstatement',
                              'requiresubmissionstatement',
                              'prog');
        $mform->setType('requiresubmissionstatement', PARAM_BOOL);

        $options = array(
            PROG_ATTEMPT_REOPEN_METHOD_NONE => get_string('attemptreopenmethod_none', 'mod_prog'),
            PROG_ATTEMPT_REOPEN_METHOD_MANUAL => get_string('attemptreopenmethod_manual', 'mod_prog'),
            PROG_ATTEMPT_REOPEN_METHOD_UNTILPASS => get_string('attemptreopenmethod_untilpass', 'mod_prog')
        );
        $mform->addElement('select', 'attemptreopenmethod', get_string('attemptreopenmethod', 'mod_prog'), $options);
        $mform->addHelpButton('attemptreopenmethod', 'attemptreopenmethod', 'mod_prog');

        $options = array(PROG_UNLIMITED_ATTEMPTS => get_string('unlimitedattempts', 'mod_prog'));
        $options += array_combine(range(1, 30), range(1, 30));
        $mform->addElement('select', 'maxattempts', get_string('maxattempts', 'mod_prog'), $options);
        $mform->addHelpButton('maxattempts', 'maxattempts', 'prog');
        $mform->disabledIf('maxattempts', 'attemptreopenmethod', 'eq', PROG_ATTEMPT_REOPEN_METHOD_NONE);

        $mform->addElement('header', 'groupsubmissionsettings', get_string('groupsubmissionsettings', 'prog'));

        $name = get_string('teamsubmission', 'prog');
        $mform->addElement('selectyesno', 'teamsubmission', $name);
        $mform->addHelpButton('teamsubmission', 'teamsubmission', 'prog');
        if ($assignment->has_submissions_or_grades()) {
            $mform->freeze('teamsubmission');
        }

        $name = get_string('preventsubmissionnotingroup', 'prog');
        $mform->addElement('selectyesno', 'preventsubmissionnotingroup', $name);
        $mform->addHelpButton('preventsubmissionnotingroup',
            'preventsubmissionnotingroup',
            'prog');
        $mform->setType('preventsubmissionnotingroup', PARAM_BOOL);
        $mform->disabledIf('preventsubmissionnotingroup', 'teamsubmission', 'eq', 0);

        $name = get_string('requireallteammemberssubmit', 'prog');
        $mform->addElement('selectyesno', 'requireallteammemberssubmit', $name);
        $mform->addHelpButton('requireallteammemberssubmit', 'requireallteammemberssubmit', 'prog');
        $mform->disabledIf('requireallteammemberssubmit', 'teamsubmission', 'eq', 0);
        $mform->disabledIf('requireallteammemberssubmit', 'submissiondrafts', 'eq', 0);

        $groupings = groups_get_all_groupings($assignment->get_course()->id);
        $options = array();
        $options[0] = get_string('none');
        foreach ($groupings as $grouping) {
            $options[$grouping->id] = $grouping->name;
        }

        $name = get_string('teamsubmissiongroupingid', 'prog');
        $mform->addElement('select', 'teamsubmissiongroupingid', $name, $options);
        $mform->addHelpButton('teamsubmissiongroupingid', 'teamsubmissiongroupingid', 'prog');
        $mform->disabledIf('teamsubmissiongroupingid', 'teamsubmission', 'eq', 0);
        if ($assignment->has_submissions_or_grades()) {
            $mform->freeze('teamsubmissiongroupingid');
        }

        $mform->addElement('header', 'notifications', get_string('notifications', 'prog'));

        $name = get_string('sendnotifications', 'prog');
        $mform->addElement('selectyesno', 'sendnotifications', $name);
        $mform->addHelpButton('sendnotifications', 'sendnotifications', 'prog');

        $name = get_string('sendlatenotifications', 'prog');
        $mform->addElement('selectyesno', 'sendlatenotifications', $name);
        $mform->addHelpButton('sendlatenotifications', 'sendlatenotifications', 'prog');
        $mform->disabledIf('sendlatenotifications', 'sendnotifications', 'eq', 1);

        $name = get_string('sendstudentnotificationsdefault', 'prog');
        $mform->addElement('selectyesno', 'sendstudentnotifications', $name);
        $mform->addHelpButton('sendstudentnotifications', 'sendstudentnotificationsdefault', 'prog');

        // Plagiarism enabling form.
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_get_form_elements_module($mform, $ctx->get_course_context(), 'mod_prog');
        }

        $this->standard_grading_coursemodule_elements();
        $name = get_string('blindmarking', 'prog');
        $mform->addElement('selectyesno', 'blindmarking', $name);
        $mform->addHelpButton('blindmarking', 'blindmarking', 'prog');
        if ($assignment->has_submissions_or_grades() ) {
            $mform->freeze('blindmarking');
        }

        $name = get_string('markingworkflow', 'prog');
        $mform->addElement('selectyesno', 'markingworkflow', $name);
        $mform->addHelpButton('markingworkflow', 'markingworkflow', 'prog');

        $name = get_string('markingallocation', 'prog');
        $mform->addElement('selectyesno', 'markingallocation', $name);
        $mform->addHelpButton('markingallocation', 'markingallocation', 'prog');
        $mform->disabledIf('markingallocation', 'markingworkflow', 'eq', 0);

        $this->standard_coursemodule_elements();
        $this->apply_admin_defaults();

        $this->add_action_buttons();

        // Add warning popup/noscript tag, if grades are changed by user.
        $hasgrade = false;
        if (!empty($this->_instance)) {
            $hasgrade = $DB->record_exists_select('prog_grades',
                                                  'assignment = ? AND grade <> -1',
                                                  array($this->_instance));
        }

        if ($mform->elementExists('grade') && $hasgrade) {
            $module = array(
                'name' => 'mod_prog',
                'fullpath' => '/mod/prog/module.js',
                'requires' => array('node', 'event'),
                'strings' => array(array('changegradewarning', 'mod_prog'))
                );
            $PAGE->requires->js_init_call('M.mod_prog.init_grade_change', null, false, $module);

            // Add noscript tag in case.
            $noscriptwarning = $mform->createElement('static',
                                                     'warning',
                                                     null,
                                                     html_writer::tag('noscript',
                                                     get_string('changegradewarning', 'mod_prog')));
            $mform->insertElementBefore($noscriptwarning, 'grade');
        }
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['allowsubmissionsfromdate'] && $data['duedate']) {
            if ($data['allowsubmissionsfromdate'] > $data['duedate']) {
                $errors['duedate'] = get_string('duedatevalidation', 'prog');
            }
        }
        if ($data['duedate'] && $data['cutoffdate']) {
            if ($data['duedate'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'prog');
            }
        }
        if ($data['allowsubmissionsfromdate'] && $data['cutoffdate']) {
            if ($data['allowsubmissionsfromdate'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'prog');
            }
        }
        if ($data['blindmarking'] && $data['attemptreopenmethod'] == PROG_ATTEMPT_REOPEN_METHOD_UNTILPASS) {
            $errors['attemptreopenmethod'] = get_string('reopenuntilpassincompatiblewithblindmarking', 'prog');
        }

        return $errors;
    }

    /**
     * Any data processing needed before the form is displayed
     * (needed to set up draft areas for editor and filemanager elements)
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('prog', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }
        $assignment = new prog($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $assignment->set_course($course);
        }

        $draftitemid = file_get_submitted_draft_itemid('introattachments');
        file_prepare_draft_area($draftitemid, $ctx->id, 'mod_prog', PROG_INTROATTACHMENT_FILEAREA,
                                0, array('subdirs' => 0));
        $defaultvalues['introattachments'] = $draftitemid;

        $assignment->plugin_data_preprocessing($defaultvalues);
    }

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completionsubmit', '', get_string('completionsubmit', 'prog'));
        return array('completionsubmit');
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }

}
