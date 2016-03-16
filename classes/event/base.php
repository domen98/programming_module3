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
 * The mod_prog abstract base event.
 *
 * @package    mod_prog
 * @copyright  2014 Mark Nelson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_prog\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_prog abstract base event class.
 *
 * Most mod_prog events can extend this class.
 *
 * @package    mod_prog
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \core\event\base {

    /** @var \prog */
    protected $prog;

    /**
     * Legacy log data.
     *
     * @var array
     */
    protected $legacylogdata;

    /**
     * Set prog instance for this event.
     * @param \prog $prog
     * @throws \coding_exception
     */
    public function set_prog(\prog $prog) {
        if ($this->is_triggered()) {
            throw new \coding_exception('set_prog() must be done before triggerring of event');
        }
        if ($prog->get_context()->id != $this->get_context()->id) {
            throw new \coding_exception('Invalid prog isntance supplied!');
        }
        $this->prog = $prog;
    }

    /**
     * Get prog instance.
     *
     * NOTE: to be used from observers only.
     *
     * @throws \coding_exception
     * @return \prog
     */
    public function get_prog() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_prog() is intended for event observers only');
        }
        if (!isset($this->prog)) {
            debugging('prog property should be initialised in each event', DEBUG_DEVELOPER);
            global $CFG;
            require_once($CFG->dirroot . '/mod/prog/locallib.php');
            $cm = get_coursemodule_from_id('prog', $this->contextinstanceid, 0, false, MUST_EXIST);
            $course = get_course($cm->course);
            $this->prog = new \prog($this->get_context(), $cm, $course);
        }
        return $this->prog;
    }


    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/prog/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Sets the legacy event log data.
     *
     * @param string $action The current action
     * @param string $info A detailed description of the change. But no more than 255 characters.
     * @param string $url The url to the prog module instance.
     */
    public function set_legacy_logdata($action = '', $info = '', $url = '') {
        $fullurl = 'view.php?id=' . $this->contextinstanceid;
        if ($url != '') {
            $fullurl .= '&' . $url;
        }

        $this->legacylogdata = array($this->courseid, 'prog', $action, $fullurl, $info, $this->contextinstanceid);
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        if (isset($this->legacylogdata)) {
            return $this->legacylogdata;
        }

        return null;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if ($this->contextlevel != CONTEXT_MODULE) {
            throw new \coding_exception('Context level must be CONTEXT_MODULE.');
        }
    }
}
