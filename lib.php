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
 * This file contains the moodle hooks for the prog module.
 *
 * It delegates most functions to the assignment class.
 *
 * @package   mod_prog
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Adds an assignment instance
 *
 * This is done by calling the add_instance() method of the assignment type class
 * @param stdClass $data
 * @param mod_prog_mod_form $form
 * @return int The instance id of the new assignment
 */
function prog_add_instance(stdClass $data, mod_prog_mod_form $form = null) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $assignment = new prog(context_module::instance($data->coursemodule), null, null);
    return $assignment->add_instance($data, true);
}

/**
 * delete an assignment instance
 * @param int $id
 * @return bool
 */
function prog_delete_instance($id) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');
    $cm = get_coursemodule_from_instance('prog', $id, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    $assignment = new prog($context, null, null);
    return $assignment->delete_instance();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all assignment submissions and feedbacks in the database
 * and clean up any related data.
 *
 * @param stdClass $data the data submitted from the reset course.
 * @return array
 */
function prog_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $status = array();
    $params = array('courseid'=>$data->courseid);
    $sql = "SELECT a.id FROM {prog} a WHERE a.course=:courseid";
    $course = $DB->get_record('course', array('id'=>$data->courseid), '*', MUST_EXIST);
    if ($progs = $DB->get_records_sql($sql, $params)) {
        foreach ($progs as $prog) {
            $cm = get_coursemodule_from_instance('prog',
                                                 $prog->id,
                                                 $data->courseid,
                                                 false,
                                                 MUST_EXIST);
            $context = context_module::instance($cm->id);
            $assignment = new prog($context, $cm, $course);
            $status = array_merge($status, $assignment->reset_userdata($data));
        }
    }
    return $status;
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every assignment event in the site is checked, else
 * only assignment events belonging to the course specified are checked.
 *
 * @param int $courseid
 * @return bool
 */
function prog_refresh_events($courseid = 0) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    if ($courseid) {
        // Make sure that the course id is numeric.
        if (!is_numeric($courseid)) {
            return false;
        }
        if (!$progs = $DB->get_records('prog', array('course' => $courseid))) {
            return false;
        }
        // Get course from courseid parameter.
        if (!$course = $DB->get_record('course', array('id' => $courseid), '*')) {
            return false;
        }
    } else {
        if (!$progs = $DB->get_records('prog')) {
            return false;
        }
    }
    foreach ($progs as $prog) {
        // Use assignment's course column if courseid parameter is not given.
        if (!$courseid) {
            $courseid = $prog->course;
            if (!$course = $DB->get_record('course', array('id' => $courseid), '*')) {
                continue;
            }
        }
        if (!$cm = get_coursemodule_from_instance('prog', $prog->id, $courseid, false)) {
            continue;
        }
        $context = context_module::instance($cm->id);
        $assignment = new prog($context, $cm, $course);
        $assignment->update_calendar($cm->id);
    }

    return true;
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid The ID of the course to reset
 * @param string $type Optional type of assignment to limit the reset to a particular assignment type
 */
function prog_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $params = array('moduletype'=>'prog', 'courseid'=>$courseid);
    $sql = 'SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
            FROM {prog} a, {course_modules} cm, {modules} m
            WHERE m.name=:moduletype AND m.id=cm.module AND cm.instance=a.id AND a.course=:courseid';

    if ($assignments = $DB->get_records_sql($sql, $params)) {
        foreach ($assignments as $assignment) {
            prog_grade_item_update($assignment, 'reset');
        }
    }
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the assignment.
 * @param moodleform $mform form passed by reference
 */
function prog_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'progheader', get_string('modulenameplural', 'prog'));
    $name = get_string('deleteallsubmissions', 'prog');
    $mform->addElement('advcheckbox', 'reset_prog_submissions', $name);
}

/**
 * Course reset form defaults.
 * @param  object $course
 * @return array
 */
function prog_reset_course_form_defaults($course) {
    return array('reset_prog_submissions'=>1);
}

/**
 * Update an assignment instance
 *
 * This is done by calling the update_instance() method of the assignment type class
 * @param stdClass $data
 * @param stdClass $form - unused
 * @return object
 */
function prog_update_instance(stdClass $data, $form) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');
    $context = context_module::instance($data->coursemodule);
    $assignment = new prog($context, null, null);
    return $assignment->update_instance($data);
}

/**
 * Return the list if Moodle features this module supports
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function prog_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_ADVANCED_GRADING:
            return true;
        case FEATURE_PLAGIARISM:
            return true;

        default:
            return null;
    }
}

/**
 * Lists all gradable areas for the advanced grading methods gramework
 *
 * @return array('string'=>'string') An array with area names as keys and descriptions as values
 */
function prog_grading_areas_list() {
    return array('submissions'=>get_string('submissions', 'prog'));
}


/**
 * extend an assigment navigation settings
 *
 * @param settings_navigation $settings
 * @param navigation_node $navref
 * @return void
 */
function prog_extend_settings_navigation(settings_navigation $settings, navigation_node $navref) {
    global $PAGE, $DB;

    $cm = $PAGE->cm;
    if (!$cm) {
        return;
    }

    $context = $cm->context;
    $course = $PAGE->course;

    if (!$course) {
        return;
    }

    // Link to gradebook.
    if (has_capability('gradereport/grader:view', $cm->context) &&
            has_capability('moodle/grade:viewall', $cm->context)) {
        $link = new moodle_url('/grade/report/grader/index.php', array('id' => $course->id));
        $linkname = get_string('viewgradebook', 'prog');
        $node = $navref->add($linkname, $link, navigation_node::TYPE_SETTING);
    }

    // Link to download all submissions.
    if (has_any_capability(array('mod/prog:grade', 'mod/prog:viewgrades'), $context)) {
        $link = new moodle_url('/mod/prog/view.php', array('id' => $cm->id, 'action'=>'grading'));
        $node = $navref->add(get_string('viewgrading', 'prog'), $link, navigation_node::TYPE_SETTING);

        $link = new moodle_url('/mod/prog/view.php', array('id' => $cm->id, 'action'=>'downloadall'));
        $node = $navref->add(get_string('downloadall', 'prog'), $link, navigation_node::TYPE_SETTING);
    }

    if (has_capability('mod/prog:revealidentities', $context)) {
        $dbparams = array('id'=>$cm->instance);
        $assignment = $DB->get_record('prog', $dbparams, 'blindmarking, revealidentities');

        if ($assignment && $assignment->blindmarking && !$assignment->revealidentities) {
            $urlparams = array('id' => $cm->id, 'action'=>'revealidentities');
            $url = new moodle_url('/mod/prog/view.php', $urlparams);
            $linkname = get_string('revealidentities', 'prog');
            $node = $navref->add($linkname, $url, navigation_node::TYPE_SETTING);
        }
    }
}

/**
 * Add a get_coursemodule_info function in case any assignment type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function prog_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    $dbparams = array('id'=>$coursemodule->instance);
    $fields = 'id, name, alwaysshowdescription, allowsubmissionsfromdate, intro, introformat';
    if (! $assignment = $DB->get_record('prog', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $assignment->name;
    if ($coursemodule->showdescription) {
        if ($assignment->alwaysshowdescription || time() > $assignment->allowsubmissionsfromdate) {
            // Convert intro to html. Do not filter cached version, filters run at display time.
            $result->content = format_module_intro('prog', $assignment, $coursemodule->id, false);
        }
    }
    return $result;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function prog_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array(
        'mod-prog-*' => get_string('page-mod-prog-x', 'prog'),
        'mod-prog-view' => get_string('page-mod-prog-view', 'prog'),
    );
    return $modulepagetype;
}

/**
 * Print an overview of all assignments
 * for the courses.
 *
 * @param mixed $courses The list of courses to print the overview for
 * @param array $htmlarray The array of html to return
 *
 * @return true
 */
function prog_print_overview($courses, &$htmlarray) {
    global $CFG, $DB;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return true;
    }

    if (!$assignments = get_all_instances_in_courses('prog', $courses)) {
        return true;
    }

    $assignmentids = array();

    // Do assignment_base::isopen() here without loading the whole thing for speed.
    foreach ($assignments as $key => $assignment) {
        $time = time();
        $isopen = false;
        if ($assignment->duedate) {
            $duedate = false;
            if ($assignment->cutoffdate) {
                $duedate = $assignment->cutoffdate;
            }
            if ($duedate) {
                $isopen = ($assignment->allowsubmissionsfromdate <= $time && $time <= $duedate);
            } else {
                $isopen = ($assignment->allowsubmissionsfromdate <= $time);
            }
        }
        if ($isopen) {
            $assignmentids[] = $assignment->id;
        }
    }

    if (empty($assignmentids)) {
        // No assignments to look at - we're done.
        return true;
    }

    // Definitely something to print, now include the constants we need.
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $strduedate = get_string('duedate', 'prog');
    $strcutoffdate = get_string('nosubmissionsacceptedafter', 'prog');
    $strnolatesubmissions = get_string('nolatesubmissions', 'prog');
    $strduedateno = get_string('duedateno', 'prog');
    $strassignment = get_string('modulename', 'prog');

    // We do all possible database work here *outside* of the loop to ensure this scales.
    list($sqlassignmentids, $assignmentidparams) = $DB->get_in_or_equal($assignmentids);

    $mysubmissions = null;
    $unmarkedsubmissions = null;

    foreach ($assignments as $assignment) {

        // Do not show assignments that are not open.
        if (!in_array($assignment->id, $assignmentids)) {
            continue;
        }

        $context = context_module::instance($assignment->coursemodule);

        // Does the submission status of the assignment require notification?
        if (has_capability('mod/prog:submit', $context)) {
            // Does the submission status of the assignment require notification?
            $submitdetails = prog_get_mysubmission_details_for_print_overview($mysubmissions, $sqlassignmentids,
                    $assignmentidparams, $assignment);
        } else {
            $submitdetails = false;
        }

        if (has_capability('mod/prog:grade', $context)) {
            // Does the grading status of the assignment require notification ?
            $gradedetails = prog_get_grade_details_for_print_overview($unmarkedsubmissions, $sqlassignmentids,
                    $assignmentidparams, $assignment, $context);
        } else {
            $gradedetails = false;
        }

        if (empty($submitdetails) && empty($gradedetails)) {
            // There is no need to display this assignment as there is nothing to notify.
            continue;
        }

        $dimmedclass = '';
        if (!$assignment->visible) {
            $dimmedclass = ' class="dimmed"';
        }
        $href = $CFG->wwwroot . '/mod/prog/view.php?id=' . $assignment->coursemodule;
        $basestr = '<div class="prog overview">' .
               '<div class="name">' .
               $strassignment . ': '.
               '<a ' . $dimmedclass .
                   'title="' . $strassignment . '" ' .
                   'href="' . $href . '">' .
               format_string($assignment->name) .
               '</a></div>';
        if ($assignment->duedate) {
            $userdate = userdate($assignment->duedate);
            $basestr .= '<div class="info">' . $strduedate . ': ' . $userdate . '</div>';
        } else {
            $basestr .= '<div class="info">' . $strduedateno . '</div>';
        }
        if ($assignment->cutoffdate) {
            if ($assignment->cutoffdate == $assignment->duedate) {
                $basestr .= '<div class="info">' . $strnolatesubmissions . '</div>';
            } else {
                $userdate = userdate($assignment->cutoffdate);
                $basestr .= '<div class="info">' . $strcutoffdate . ': ' . $userdate . '</div>';
            }
        }

        // Show only relevant information.
        if (!empty($submitdetails)) {
            $basestr .= $submitdetails;
        }

        if (!empty($gradedetails)) {
            $basestr .= $gradedetails;
        }
        $basestr .= '</div>';

        if (empty($htmlarray[$assignment->course]['prog'])) {
            $htmlarray[$assignment->course]['prog'] = $basestr;
        } else {
            $htmlarray[$assignment->course]['prog'] .= $basestr;
        }
    }
    return true;
}

/**
 * This api generates html to be displayed to students in print overview section, related to their submission status of the given
 * assignment.
 *
 * @param array $mysubmissions list of submissions of current user indexed by assignment id.
 * @param string $sqlassignmentids sql clause used to filter open assignments.
 * @param array $assignmentidparams sql params used to filter open assignments.
 * @param stdClass $assignment current assignment
 *
 * @return bool|string html to display , false if nothing needs to be displayed.
 * @throws coding_exception
 */
function prog_get_mysubmission_details_for_print_overview(&$mysubmissions, $sqlassignmentids, $assignmentidparams,
                                                            $assignment) {
    global $USER, $DB;

    if ($assignment->nosubmissions) {
        // Offline assignment. No need to display alerts for offline assignments.
        return false;
    }

    $strnotsubmittedyet = get_string('notsubmittedyet', 'prog');

    if (!isset($mysubmissions)) {

        // Get all user submissions, indexed by assignment id.
        $dbparams = array_merge(array($USER->id), $assignmentidparams, array($USER->id));
        $mysubmissions = $DB->get_records_sql('SELECT a.id AS assignment,
                                                      a.nosubmissions AS nosubmissions,
                                                      g.timemodified AS timemarked,
                                                      g.grader AS grader,
                                                      g.grade AS grade,
                                                      s.status AS status
                                                 FROM {prog} a, {prog_submission} s
                                            LEFT JOIN {prog_grades} g ON
                                                      g.assignment = s.assignment AND
                                                      g.userid = ? AND
                                                      g.attemptnumber = s.attemptnumber
                                                WHERE a.id ' . $sqlassignmentids . ' AND
                                                      s.latest = 1 AND
                                                      s.assignment = a.id AND
                                                      s.userid = ?', $dbparams);
    }

    $submitdetails = '';
    $submitdetails .= '<div class="details">';
    $submitdetails .= get_string('mysubmission', 'prog');
    $submission = false;

    if (isset($mysubmissions[$assignment->id])) {
        $submission = $mysubmissions[$assignment->id];
    }

    if ($submission && $submission->status == PROG_SUBMISSION_STATUS_SUBMITTED) {
        // A valid submission already exists, no need to notify students about this.
        return false;
    }

    // We need to show details only if a valid submission doesn't exist.
    if (!$submission ||
        !$submission->status ||
        $submission->status == PROG_SUBMISSION_STATUS_DRAFT ||
        $submission->status == PROG_SUBMISSION_STATUS_NEW
    ) {
        $submitdetails .= $strnotsubmittedyet;
    } else {
        $submitdetails .= get_string('submissionstatus_' . $submission->status, 'prog');
    }
    if ($assignment->markingworkflow) {
        $workflowstate = $DB->get_field('prog_user_flags', 'workflowstate', array('assignment' =>
                $assignment->id, 'userid' => $USER->id));
        if ($workflowstate) {
            $gradingstatus = 'markingworkflowstate' . $workflowstate;
        } else {
            $gradingstatus = 'markingworkflowstate' . PROG_MARKING_WORKFLOW_STATE_NOTMARKED;
        }
    } else if (!empty($submission->grade) && $submission->grade !== null && $submission->grade >= 0) {
        $gradingstatus = PROG_GRADING_STATUS_GRADED;
    } else {
        $gradingstatus = PROG_GRADING_STATUS_NOT_GRADED;
    }
    $submitdetails .= ', ' . get_string($gradingstatus, 'prog');
    $submitdetails .= '</div>';
    return $submitdetails;
}

/**
 * This api generates html to be displayed to teachers in print overview section, related to the grading status of the given
 * assignment's submissions.
 *
 * @param array $unmarkedsubmissions list of submissions of that are currently unmarked indexed by assignment id.
 * @param string $sqlassignmentids sql clause used to filter open assignments.
 * @param array $assignmentidparams sql params used to filter open assignments.
 * @param stdClass $assignment current assignment
 * @param context $context context of the assignment.
 *
 * @return bool|string html to display , false if nothing needs to be displayed.
 * @throws coding_exception
 */
function prog_get_grade_details_for_print_overview(&$unmarkedsubmissions, $sqlassignmentids, $assignmentidparams,
                                                     $assignment, $context) {
    global $DB;
    if (!isset($unmarkedsubmissions)) {
        // Build up and array of unmarked submissions indexed by assignment id/ userid
        // for use where the user has grading rights on assignment.
        $dbparams = array_merge(array(PROG_SUBMISSION_STATUS_SUBMITTED), $assignmentidparams);
        $rs = $DB->get_recordset_sql('SELECT s.assignment as assignment,
                                             s.userid as userid,
                                             s.id as id,
                                             s.status as status,
                                             g.timemodified as timegraded
                                        FROM {prog_submission} s
                                   LEFT JOIN {prog_grades} g ON
                                             s.userid = g.userid AND
                                             s.assignment = g.assignment AND
                                             g.attemptnumber = s.attemptnumber
                                       WHERE
                                             ( g.timemodified is NULL OR
                                             s.timemodified > g.timemodified OR
                                             g.grade IS NULL ) AND
                                             s.timemodified IS NOT NULL AND
                                             s.status = ? AND
                                             s.latest = 1 AND
                                             s.assignment ' . $sqlassignmentids, $dbparams);

        $unmarkedsubmissions = array();
        foreach ($rs as $rd) {
            $unmarkedsubmissions[$rd->assignment][$rd->userid] = $rd->id;
        }
        $rs->close();
    }

    // Count how many people can submit.
    $submissions = 0;
    if ($students = get_enrolled_users($context, 'mod/prog:view', 0, 'u.id')) {
        foreach ($students as $student) {
            if (isset($unmarkedsubmissions[$assignment->id][$student->id])) {
                $submissions++;
            }
        }
    }

    if ($submissions) {
        $urlparams = array('id' => $assignment->coursemodule, 'action' => 'grading');
        $url = new moodle_url('/mod/prog/view.php', $urlparams);
        $gradedetails = '<div class="details">' .
                '<a href="' . $url . '">' .
                get_string('submissionsnotgraded', 'prog', $submissions) .
                '</a></div>';
        return $gradedetails;
    } else {
        return false;
    }

}

/**
 * Print recent activity from all assignments in a given course
 *
 * This is used by the recent activity block
 * @param mixed $course the course to print activity for
 * @param bool $viewfullnames boolean to determine whether to show full names or not
 * @param int $timestart the time the rendering started
 * @return bool true if activity was printed, false otherwise.
 */
function prog_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    // Do not use log table if possible, it may be huge.

    $dbparams = array($timestart, $course->id, 'prog', PROG_SUBMISSION_STATUS_SUBMITTED);
    $namefields = user_picture::fields('u', null, 'userid');
    if (!$submissions = $DB->get_records_sql("SELECT asb.id, asb.timemodified, cm.id AS cmid,
                                                     $namefields
                                                FROM {prog_submission} asb
                                                     JOIN {prog} a      ON a.id = asb.assignment
                                                     JOIN {course_modules} cm ON cm.instance = a.id
                                                     JOIN {modules} md        ON md.id = cm.module
                                                     JOIN {user} u            ON u.id = asb.userid
                                               WHERE asb.timemodified > ? AND
                                                     asb.latest = 1 AND
                                                     a.course = ? AND
                                                     md.name = ? AND
                                                     asb.status = ?
                                            ORDER BY asb.timemodified ASC", $dbparams)) {
         return false;
    }

    $modinfo = get_fast_modinfo($course);
    $show    = array();
    $grader  = array();

    $showrecentsubmissions = get_config('prog', 'showrecentsubmissions');

    foreach ($submissions as $submission) {
        if (!array_key_exists($submission->cmid, $modinfo->get_cms())) {
            continue;
        }
        $cm = $modinfo->get_cm($submission->cmid);
        if (!$cm->uservisible) {
            continue;
        }
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }

        $context = context_module::instance($submission->cmid);
        // The act of submitting of assignment may be considered private -
        // only graders will see it if specified.
        if (empty($showrecentsubmissions)) {
            if (!array_key_exists($cm->id, $grader)) {
                $grader[$cm->id] = has_capability('moodle/grade:viewall', $context);
            }
            if (!$grader[$cm->id]) {
                continue;
            }
        }

        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($groupmode == SEPARATEGROUPS &&
                !has_capability('moodle/site:accessallgroups',  $context)) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (!$modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups =  groups_get_all_groups($course->id, $submission->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newsubmissions', 'prog').':', 3);

    foreach ($show as $submission) {
        $cm = $modinfo->get_cm($submission->cmid);
        $context = context_module::instance($submission->cmid);
        $prog = new prog($context, $cm, $cm->course);
        $link = $CFG->wwwroot.'/mod/prog/view.php?id='.$cm->id;
        // Obscure first and last name if blind marking enabled.
        if ($prog->is_blind_marking()) {
            $submission->firstname = get_string('participant', 'mod_prog');
            $submission->lastname = $prog->get_uniqueid_for_user($submission->userid);
        }
        print_recent_activity_note($submission->timemodified,
                                   $submission,
                                   $cm->name,
                                   $link,
                                   false,
                                   $viewfullnames);
    }

    return true;
}

/**
 * Returns all assignments since a given time.
 *
 * @param array $activities The activity information is returned in this array
 * @param int $index The current index in the activities array
 * @param int $timestart The earliest activity to show
 * @param int $courseid Limit the search to this course
 * @param int $cmid The course module id
 * @param int $userid Optional user id
 * @param int $groupid Optional group id
 * @return void
 */
function prog_get_recent_mod_activity(&$activities,
                                        &$index,
                                        $timestart,
                                        $courseid,
                                        $cmid,
                                        $userid=0,
                                        $groupid=0) {
    global $CFG, $COURSE, $USER, $DB;

    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id'=>$courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->get_cm($cmid);
    $params = array();
    if ($userid) {
        $userselect = 'AND u.id = :userid';
        $params['userid'] = $userid;
    } else {
        $userselect = '';
    }

    if ($groupid) {
        $groupselect = 'AND gm.groupid = :groupid';
        $groupjoin   = 'JOIN {groups_members} gm ON  gm.userid=u.id';
        $params['groupid'] = $groupid;
    } else {
        $groupselect = '';
        $groupjoin   = '';
    }

    $params['cminstance'] = $cm->instance;
    $params['timestart'] = $timestart;
    $params['submitted'] = PROG_SUBMISSION_STATUS_SUBMITTED;

    $userfields = user_picture::fields('u', null, 'userid');

    if (!$submissions = $DB->get_records_sql('SELECT asb.id, asb.timemodified, ' .
                                                     $userfields .
                                             '  FROM {prog_submission} asb
                                                JOIN {prog} a ON a.id = asb.assignment
                                                JOIN {user} u ON u.id = asb.userid ' .
                                          $groupjoin .
                                            '  WHERE asb.timemodified > :timestart AND
                                                     asb.status = :submitted AND
                                                     a.id = :cminstance
                                                     ' . $userselect . ' ' . $groupselect .
                                            ' ORDER BY asb.timemodified ASC', $params)) {
         return;
    }

    $groupmode       = groups_get_activity_groupmode($cm, $course);
    $cmcontext      = context_module::instance($cm->id);
    $grader          = has_capability('moodle/grade:viewall', $cmcontext);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cmcontext);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $cmcontext);


    $showrecentsubmissions = get_config('prog', 'showrecentsubmissions');
    $show = array();
    foreach ($submissions as $submission) {
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }
        // The act of submitting of assignment may be considered private -
        // only graders will see it if specified.
        if (empty($showrecentsubmissions)) {
            if (!$grader) {
                continue;
            }
        }

        if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (!$modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups =  groups_get_all_groups($course->id, $submission->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return;
    }

    if ($grader) {
        require_once($CFG->libdir.'/gradelib.php');
        $userids = array();
        foreach ($show as $id => $submission) {
            $userids[] = $submission->userid;
        }
        $grades = grade_get_grades($courseid, 'mod', 'prog', $cm->instance, $userids);
    }

    $aname = format_string($cm->name, true);
    foreach ($show as $submission) {
        $activity = new stdClass();

        $activity->type         = 'prog';
        $activity->cmid         = $cm->id;
        $activity->name         = $aname;
        $activity->sectionnum   = $cm->sectionnum;
        $activity->timestamp    = $submission->timemodified;
        $activity->user         = new stdClass();
        if ($grader) {
            $activity->grade = $grades->items[0]->grades[$submission->userid]->str_long_grade;
        }

        $userfields = explode(',', user_picture::fields());
        foreach ($userfields as $userfield) {
            if ($userfield == 'id') {
                // Aliased in SQL above.
                $activity->user->{$userfield} = $submission->userid;
            } else {
                $activity->user->{$userfield} = $submission->{$userfield};
            }
        }
        $activity->user->fullname = fullname($submission, $viewfullnames);

        $activities[$index++] = $activity;
    }

    return;
}

/**
 * Print recent activity from all assignments in a given course
 *
 * This is used by course/recent.php
 * @param stdClass $activity
 * @param int $courseid
 * @param bool $detail
 * @param array $modnames
 */
function prog_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="assignment-recent">';

    echo '<tr><td class="userpicture" valign="top">';
    echo $OUTPUT->user_picture($activity->user);
    echo '</td><td>';

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo '<img src="' . $OUTPUT->pix_url('icon', 'prog') . '" '.
             'class="icon" alt="' . $modname . '">';
        echo '<a href="' . $CFG->wwwroot . '/mod/prog/view.php?id=' . $activity->cmid . '">';
        echo $activity->name;
        echo '</a>';
        echo '</div>';
    }

    if (isset($activity->grade)) {
        echo '<div class="grade">';
        echo get_string('grade').': ';
        echo $activity->grade;
        echo '</div>';
    }

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->id}&amp;course=$courseid\">";
    echo "{$activity->user->fullname}</a>  - " . userdate($activity->timestamp);
    echo '</div>';

    echo '</td></tr></table>';
}

/**
 * Checks if a scale is being used by an assignment.
 *
 * This is used by the backup code to decide whether to back up a scale
 * @param int $assignmentid
 * @param int $scaleid
 * @return boolean True if the scale is used by the assignment
 */
function prog_scale_used($assignmentid, $scaleid) {
    global $DB;

    $return = false;
    $rec = $DB->get_record('prog', array('id'=>$assignmentid, 'grade'=>-$scaleid));

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of assignment
 *
 * This is used to find out if scale used anywhere
 * @param int $scaleid
 * @return boolean True if the scale is used by any assignment
 */
function prog_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('prog', array('grade'=>-$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function prog_get_view_actions() {
    return array('view submission', 'view feedback');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function prog_get_post_actions() {
    return array('upload', 'submit', 'submit for grading');
}

/**
 * Call cron on the prog module.
 */
function prog_cron() {
    global $CFG;

    require_once($CFG->dirroot . '/mod/prog/locallib.php');
    prog::cron();

    $plugins = core_component::get_plugin_list('progsubmission');

    foreach ($plugins as $name => $plugin) {
        $disabled = get_config('progsubmission_' . $name, 'disabled');
        if (!$disabled) {
            $class = 'prog_submission_' . $name;
            require_once($CFG->dirroot . '/mod/prog/submission/' . $name . '/locallib.php');
            $class::cron();
        }
    }
    $plugins = core_component::get_plugin_list('progfeedback');

    foreach ($plugins as $name => $plugin) {
        $disabled = get_config('progfeedback_' . $name, 'disabled');
        if (!$disabled) {
            $class = 'prog_feedback_' . $name;
            require_once($CFG->dirroot . '/mod/prog/feedback/' . $name . '/locallib.php');
            $class::cron();
        }
    }

    return true;
}

/**
 * Returns all other capabilities used by this module.
 * @return array Array of capability strings
 */
function prog_get_extra_capabilities() {
    return array('gradereport/grader:view',
                 'moodle/grade:viewall',
                 'moodle/site:viewfullnames',
                 'moodle/site:config');
}

/**
 * Create grade item for given assignment.
 *
 * @param stdClass $prog record with extra cmidnumber
 * @param array $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function prog_grade_item_update($prog, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($prog->courseid)) {
        $prog->courseid = $prog->course;
    }

    $params = array('itemname'=>$prog->name, 'idnumber'=>$prog->cmidnumber);

    // Check if feedback plugin for gradebook is enabled, if yes then
    // gradetype = GRADE_TYPE_TEXT else GRADE_TYPE_NONE.
    $gradefeedbackenabled = false;

    if (isset($prog->gradefeedbackenabled)) {
        $gradefeedbackenabled = $prog->gradefeedbackenabled;
    } else if ($prog->grade == 0) { // Grade feedback is needed only when grade == 0.
        require_once($CFG->dirroot . '/mod/prog/locallib.php');
        $mod = get_coursemodule_from_instance('prog', $prog->id, $prog->courseid);
        $cm = context_module::instance($mod->id);
        $assignment = new prog($cm, null, null);
        $gradefeedbackenabled = $assignment->is_gradebook_feedback_enabled();
    }

    if ($prog->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $prog->grade;
        $params['grademin']  = 0;

    } else if ($prog->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$prog->grade;

    } else if ($gradefeedbackenabled) {
        // $prog->grade == 0 and feedback enabled.
        $params['gradetype'] = GRADE_TYPE_TEXT;
    } else {
        // $prog->grade == 0 and no feedback enabled.
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/prog',
                        $prog->courseid,
                        'mod',
                        'prog',
                        $prog->id,
                        0,
                        $grades,
                        $params);
}

/**
 * Return grade for given user or all users.
 *
 * @param stdClass $prog record of prog with an additional cmidnumber
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function prog_get_user_grades($prog, $userid=0) {
    global $CFG;

    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $cm = get_coursemodule_from_instance('prog', $prog->id, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $assignment = new prog($context, null, null);
    $assignment->set_instance($prog);
    return $assignment->get_user_grades_for_gradebook($userid);
}

/**
 * Update activity grades.
 *
 * @param stdClass $prog database record
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone - not used
 */
function prog_update_grades($prog, $userid=0, $nullifnone=true) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if ($prog->grade == 0) {
        prog_grade_item_update($prog);

    } else if ($grades = prog_get_user_grades($prog, $userid)) {
        foreach ($grades as $k => $v) {
            if ($v->rawgrade == -1) {
                $grades[$k]->rawgrade = null;
            }
        }
        prog_grade_item_update($prog, $grades);

    } else {
        prog_grade_item_update($prog);
    }
}

/**
 * List the file areas that can be browsed.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array
 */
function prog_get_file_areas($course, $cm, $context) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $areas = array(PROG_INTROATTACHMENT_FILEAREA => get_string('introattachments', 'mod_prog'));

    $assignment = new prog($context, $cm, $course);
    foreach ($assignment->get_submission_plugins() as $plugin) {
        if ($plugin->is_visible()) {
            $pluginareas = $plugin->get_file_areas();

            if ($pluginareas) {
                $areas = array_merge($areas, $pluginareas);
            }
        }
    }
    foreach ($assignment->get_feedback_plugins() as $plugin) {
        if ($plugin->is_visible()) {
            $pluginareas = $plugin->get_file_areas();

            if ($pluginareas) {
                $areas = array_merge($areas, $pluginareas);
            }
        }
    }

    return $areas;
}

/**
 * File browsing support for prog module.
 *
 * @param file_browser $browser
 * @param object $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return object file_info instance or null if not found
 */
function prog_get_file_info($browser,
                              $areas,
                              $course,
                              $cm,
                              $context,
                              $filearea,
                              $itemid,
                              $filepath,
                              $filename) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;

    // Need to find where this belongs to.
    $assignment = new prog($context, $cm, $course);
    if ($filearea === PROG_INTROATTACHMENT_FILEAREA) {
        if (!has_capability('moodle/course:managefiles', $context)) {
            // Students can not peak here!
            return null;
        }
        if (!($storedfile = $fs->get_file($assignment->get_context()->id,
                                          'mod_prog', $filearea, 0, $filepath, $filename))) {
            return null;
        }
        return new file_info_stored($browser,
                        $assignment->get_context(),
                        $storedfile,
                        $urlbase,
                        $filearea,
                        $itemid,
                        true,
                        true,
                        false);
    }

    $pluginowner = null;
    foreach ($assignment->get_submission_plugins() as $plugin) {
        if ($plugin->is_visible()) {
            $pluginareas = $plugin->get_file_areas();

            if (array_key_exists($filearea, $pluginareas)) {
                $pluginowner = $plugin;
                break;
            }
        }
    }
    if (!$pluginowner) {
        foreach ($assignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible()) {
                $pluginareas = $plugin->get_file_areas();

                if (array_key_exists($filearea, $pluginareas)) {
                    $pluginowner = $plugin;
                    break;
                }
            }
        }
    }

    if (!$pluginowner) {
        return null;
    }

    $result = $pluginowner->get_file_info($browser, $filearea, $itemid, $filepath, $filename);
    return $result;
}

/**
 * Prints the complete info about a user's interaction with an assignment.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $coursemodule
 * @param stdClass $prog the database prog record
 *
 * This prints the submission summary and feedback summary for this student.
 */
function prog_user_complete($course, $user, $coursemodule, $prog) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $context = context_module::instance($coursemodule->id);

    $assignment = new prog($context, $coursemodule, $course);

    echo $assignment->view_student_summary($user, false);
}

/**
 * Print the grade information for the assignment for this user.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $coursemodule
 * @param stdClass $assignment
 */
function prog_user_outline($course, $user, $coursemodule, $assignment) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/grade/grading/lib.php');

    $gradinginfo = grade_get_grades($course->id,
                                        'mod',
                                        'prog',
                                        $assignment->id,
                                        $user->id);

    $gradingitem = $gradinginfo->items[0];
    $gradebookgrade = $gradingitem->grades[$user->id];

    if (empty($gradebookgrade->str_long_grade)) {
        return null;
    }
    $result = new stdClass();
    $result->info = get_string('outlinegrade', 'prog', $gradebookgrade->str_long_grade);
    $result->time = $gradebookgrade->dategraded;

    return $result;
}

/**
 * Obtains the automatic completion state for this module based on any conditions
 * in prog settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function prog_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/prog/locallib.php');

    $prog = new prog(null, $cm, $course);

    // If completion option is enabled, evaluate it and return true/false.
    if ($prog->get_instance()->completionsubmit) {
        $submission = $prog->get_user_submission($userid, false);
        return $submission && $submission->status == PROG_SUBMISSION_STATUS_SUBMITTED;
    } else {
        // Completion option is not enabled so just return $type.
        return $type;
    }
}

/**
 * Serves intro attachment files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function prog_pluginfile($course,
                $cm,
                context $context,
                $filearea,
                $args,
                $forcedownload,
                array $options=array()) {
    global $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);
    if (!has_capability('mod/prog:view', $context)) {
        return false;
    }

    require_once($CFG->dirroot . '/mod/prog/locallib.php');
    $prog = new prog($context, $cm, $course);

    if ($filearea !== PROG_INTROATTACHMENT_FILEAREA) {
        return false;
    }
    if (!$prog->show_intro()) {
        return false;
    }

    $itemid = (int)array_shift($args);
    if ($itemid != 0) {
        return false;
    }

    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/mod_prog/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}
